import {Injectable} from '@angular/core';
import * as moment from 'moment';
import * as XLSX from 'xlsx';
import {Observable} from 'rxjs';

@Injectable()
export class CommonService {
  constructor() {
  }

  public isDateValid(dateStr: string): boolean {
    if (!dateStr.match('(^[0-9]{4}$)|(^[0-9]{4}-[0-9]{2}$)|(^[0-9]{4}-[0-9]{2}-[0-9]{2}$)')) {
      return false;
    }

    return moment(dateStr).isValid();
  }

  public kvArrToMap(arr, key = 'id', value = 'value') {
    const data = {};

    for (const i of arr) {
      data[i[key]] = i[value];
    }

    return data;
  }

  public getSpreadsheetData(file: File): Observable<Array<Array<any>>> {
    return new Observable(observer => {
      const reader: FileReader = new FileReader();
      reader.onload = (e: any) => {
        /* read workbook */
        const bstr: string = e.target.result;
        const wb: XLSX.WorkBook = XLSX.read(bstr, {type: 'binary'});

        /* grab first sheet */
        const wsname: string = wb.SheetNames[0];
        const ws: XLSX.WorkSheet = wb.Sheets[wsname];

        /* save data */
        const data = <Array<Array<any>>>(XLSX.utils.sheet_to_json(ws, {header: 1}));
        observer.next(data);
        observer.complete();
      };
      reader.readAsBinaryString(file);
    });
  }

  public isImageFile(fileName: string): boolean {
    return fileName.match('^.+?\\.(jpeg|png|bmp|gif|svg|jpg)$') !== null;
  }

  public isSpreadsheetFile(fileName: string): boolean {
    return fileName.match('^.+?\\.(xlsx|xls|csv)$') !== null;
  }

  public getFormatByDateStr(date: string): string {
    if (date.match('^[0-9]{4}$')) {
      return 'YYYY';
    } else if (date.match('^[0-9]{4}-[0-9]{2}$')) {
      return 'YYYY-MM';
    } else if (date.match('^[0-9]{4}-[0-9]{2}-[0-9]{2}$')) {
      return 'YYYY-MM-DD';
    } else {
      throw new Error('Invalid date');
    }
  }
}
