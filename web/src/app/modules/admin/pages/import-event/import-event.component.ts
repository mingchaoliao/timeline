import {Component, OnInit} from '@angular/core';
import {FormBuilder, FormGroup} from '@angular/forms';
import {CommonService} from '../../../core/shared/services/common.service';
import {concat, Observable} from 'rxjs';
import {Router} from '@angular/router';
import {PeriodService} from '../../../core/shared/services/period.service';
import {CatalogService} from '../../../core/shared/services/catalog.service';
import {DateAttributeService} from '../../../core/shared/services/dateAttribute.service';
import {ImageService} from '../../../core/shared/services/image.service';
import {EventService} from '../../../core/shared/services/event.service';

@Component({
  selector: 'app-import-event',
  templateUrl: './import-event.component.html',
  styleUrls: ['./import-event.component.css']
})
export class ImportEventComponent implements OnInit {

  public importEventForm: FormGroup;
  public eventsData: Array<any> = [];
  public errorMessage: string = null;
  public files: Array<File> = [];
  public isUploading = false;
  public percentage = 0;

  constructor(
    private formBuilder: FormBuilder,
    private common: CommonService,
    private router: Router,
    private periodService: PeriodService,
    private catalogService: CatalogService,
    private dateAttributeService: DateAttributeService,
    private imageService: ImageService,
    private eventService: EventService
  ) {
    this.importEventForm = formBuilder.group({
      'files': [null, []],
    });
  }

  ngOnInit() {
  }

  filesChanged(event) {
    this.eventsData = [];
    this.errorMessage = null;
    this.files = event.srcElement.files;
    let hasError = false;
    let spreadsheetFile = null;
    const photoFilesKvMap = {};

    for (const file of this.files) {
      const isImageFile = this.common.isImageFile(file.name);
      const isSpreadsheetFile = this.common.isSpreadsheetFile(file.name);
      if (!isImageFile && !isSpreadsheetFile) {
        hasError = true;
        return;
      }
      if (isImageFile) {
        photoFilesKvMap[file.name] = file;
      }
      if (isSpreadsheetFile) {
        spreadsheetFile = file;
      }
    }

    if (spreadsheetFile === null) {
      hasError = true;
      this.errorMessage = 'Spreadsheet file is required';
    }

    if (!hasError) {
      this.common.getSpreadsheetData(spreadsheetFile).subscribe(
        s => {
          try {
            this.eventsData = this.parseSpreadsheetData(s, photoFilesKvMap);
          } catch (e) {
            this.errorMessage = e.message;
          }
        },
        e => {
          this.errorMessage = 'Failed to parse data in the spreadsheet';
        }
      );
    }

  }

  onSubmit() {
    this.isUploading = true;
    const imageKvMap: any = {};

    const uploadImageObservers: Array<Observable<any>> = [];
    for (const file of this.files) {
      const fileName = file.name;
      if (this.common.isImageFile(fileName)) {
        uploadImageObservers.push(this.imageService.upload(file));
      }
    }

    const periods: Array<string> = [];
    const dateAttributes: Array<string> = [];
    let catalogs: Array<string> = [];
    let periodKvMap: any = {};
    let dateAttributeKvMap: any = {};
    let catalogKvMap: any = {};

    for (const event of this.eventsData) {
      if (event['period']) {
        periods.push(event['period']);
      }
      if (event['catalogs']) {
        catalogs = catalogs.concat(event['catalogs']);
      }
      if (event['startDateAttribute']) {
        dateAttributes.push(event['startDateAttribute']);
      }
      if (event['endDateAttribute']) {
        dateAttributes.push(event['endDateAttribute']);
      }
    }

    const totalNumOfTasks = uploadImageObservers.length + 6;
    let numOfCompletedTasks = 0;

    concat(...uploadImageObservers).subscribe(
      imageUploadReceipt => {
        imageKvMap[imageUploadReceipt.originalName] = imageUploadReceipt.id;
        this.percentage = Math.floor(++numOfCompletedTasks / totalNumOfTasks * 100);
      },
      e => this.handleError(e),
      () => {
        this.percentage = Math.floor(++numOfCompletedTasks / totalNumOfTasks * 100);
        this.periodService.bulkCreate(periods).subscribe(
          periods => {
            periodKvMap = this.common.kvArrToMap(
              periods,
              'value',
              'id'
            );
            this.percentage = Math.floor(++numOfCompletedTasks / totalNumOfTasks * 100);
            this.dateAttributeService.bulkCreate(dateAttributes).subscribe(
              dateAttributes => {
                dateAttributeKvMap = this.common.kvArrToMap(dateAttributes, 'value', 'id');
                this.percentage = Math.floor(++numOfCompletedTasks / totalNumOfTasks * 100);
                this.catalogService.bulkCreate(catalogs).subscribe(
                  catalogs => {
                    catalogKvMap = this.common.kvArrToMap(catalogs, 'value', 'id');
                    for (let i = 0; i < this.eventsData.length; i++) {
                      if (this.eventsData[i]['startDateAttribute']) {
                        this.eventsData[i]['startDateAttributeId'] = dateAttributeKvMap[this.eventsData[i]['startDateAttribute']];
                      }
                      if (this.eventsData[i]['endDateAttribute']) {
                        this.eventsData[i]['endDateAttributeId'] = dateAttributeKvMap[this.eventsData[i]['endDateAttribute']];
                      }
                      if (this.eventsData[i]['period']) {
                        this.eventsData[i]['periodId'] = periodKvMap[this.eventsData[i]['period']];
                      }
                      this.eventsData[i].imageIds = [];
                      for (let j = 0; j < this.eventsData[i].images.length; j++) {
                        this.eventsData[i].imageIds.push(imageKvMap[this.eventsData[i].images[j]['name']]);
                      }
                      this.eventsData[i].catalogIds = [];
                      for (let j = 0; j < this.eventsData[i].catalogs.length; j++) {
                        this.eventsData[i].catalogIds.push(catalogKvMap[this.eventsData[i].catalogs[j]]);
                      }
                      this.eventsData[i].catalogs = this.eventsData[i].catalogIds;
                    }
                    this.percentage = Math.floor(++numOfCompletedTasks / totalNumOfTasks * 100);
                    this.eventService.bulkCreate(this.eventsData).subscribe(
                      success => {
                        this.percentage = Math.floor(++numOfCompletedTasks / totalNumOfTasks * 100);
                        this.router.navigate(['/']);
                      },
                      e => this.handleError(e)
                    );
                  },
                  e => this.handleError(e)
                );
              },
              e => this.handleError(e)
            );
          },
          e => this.handleError(e)
        );
      }
    );
  }

  private handleError(error: Error) {
    this.percentage = 0;
    this.isUploading = false;
    this.errorMessage = error.message;
    this.eventsData = [];
    this.importEventForm.reset();
  }

  private parseSpreadsheetData(sheet: Array<Array<any>>, imageFileKvMap: any): Array<any> {
    if (sheet.length === 0) {
      throw new Error('Must contains header row');
    }

    if (sheet.length === 1) {
      throw new Error('Must contains at least one record.');
    }

    sheet.splice(0, 1);
    let data = [];

    for (let i = 0; i < sheet.length; i++) {
      const startDateStr: string = this.trim(sheet[i][0]);
      if (startDateStr === null) {
        this.makeError('A', i + 2, 'startDate is required');
      }
      const startDate = this.constructEventDate(startDateStr);
      if (!this.common.isDateValid(startDateStr)) {
        this.makeError('A', i + 2, 'invalid startDate');
      }
      const startDateAttribute: string = this.trim(sheet[i][1]);

      const endDateStr: string = this.trim(sheet[i][2]);
      let endDateAttribute: string = null;
      let endDate = null;
      if (endDateStr !== null) {
        if (!this.common.isDateValid(endDateStr)) {
          this.makeError('C', i + 2, 'invalid endDate');
        }
        endDateAttribute = this.trim(sheet[i][3]);
        endDate = this.constructEventDate(endDateStr);
      }

      const content: string = this.trim(sheet[i][4]);
      if (content === null) {
        this.makeError('E', i + 2, 'content is required');
      }

      const period: string = this.trim(sheet[i][5]);

      const catalogStr: string = this.trim(sheet[i][6]);
      let catalogs: Array<string> = [];
      if (catalogStr !== null) {
        catalogs = catalogStr.split(/;|；/);
        for (let c = catalogs.length - 1; c >= 0; c--) {
          catalogs[c] = catalogs[c].trim();
          if (catalogs[c] === null) {
            catalogs.splice(c, 1);
          }
        }
      }

      let images = [];

      const imageStr1: string = this.trim(sheet[i][7]);
      let imageDescription1: string = null;
      if (imageStr1 !== null) {
        if (imageFileKvMap[imageStr1] === undefined) {
          this.makeError('H', i + 2, 'image file "' + imageStr1 + '" does not exist');
        }
        imageDescription1 = this.trim(sheet[i][8]);
        images.push({
          name: imageStr1,
          description: imageDescription1
        });
      }

      const imageStr2: string = this.trim(sheet[i][9]);
      let imageDescription2: string = null;
      if (imageStr2 !== null) {
        if (imageFileKvMap[imageStr2] === undefined) {
          this.makeError('J', i + 2, 'image file "' + imageStr2 + '" does not exist');
        }
        imageDescription2 = this.trim(sheet[i][10]);
        images.push({
          name: imageStr2,
          description: imageDescription2
        });
      }

      const imageStr3: string = this.trim(sheet[i][11]);
      let imageDescription3: string = null;
      if (imageStr3 !== null) {
        if (imageFileKvMap[imageStr3] === undefined) {
          this.makeError('H', i + 2, 'image file "' + imageStr3 + '" does not exist');
        }
        imageDescription3 = this.trim(sheet[i][12]);
        images.push({
          name: imageStr3,
          description: imageDescription3
        });
      }

      data.push({
        startDate: startDate,
        startDateAttribute: startDateAttribute,
        endDate: endDate,
        endDateAttribute: endDateAttribute,
        content: content,
        period: period,
        catalogs: catalogs,
        images: images
      });
    }
    return data;
  }

  private constructEventDate(str: string) {
    const parts = str.split('-');

    const date = {
      year: Number(parts[0])
    };

    if (parts[1]) {
      date['month'] = Number(parts[1]);
    }

    if (parts[2]) {
      date['day'] = Number(parts[2]);
    }

    return date;
  }

  private trim(str: string): string {
    if (str === null || str === undefined) {
      return null;
    }
    return str.trim();
  }

  private makeError(col: string, row: number, message: string) {
    throw new Error(message + ' (at cell ' + col + row + ')');
  }
}
