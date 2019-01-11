import {Component, EventEmitter, OnInit} from '@angular/core';
import {Catalog} from '../../../core/shared/models/catalog';
import {NgbModal, NgbModalRef} from '@ng-bootstrap/ng-bootstrap';
import {CatalogService} from '../../../core/shared/services/catalog.service';
import {Notification} from '../../../core/shared/models/notification';
import {NotificationEmitter} from '../../../core/shared/events/notificationEmitter';

@Component({
  selector: 'app-configure-catalogs',
  templateUrl: './configure-catalogs.component.html',
  styleUrls: ['./configure-catalogs.component.css']
})
export class ConfigureCatalogsComponent implements OnInit {
  public catalogs: Array<Catalog> = null;
  public createModalRef: NgbModalRef = null;

  constructor(
    public catalogService: CatalogService,
    public modalService: NgbModal
  ) {
    catalogService.get().subscribe(
      s => {
        this.catalogs = s;
      },
      error => {
        NotificationEmitter.emit(Notification.error(error.error.message, 'Unable to retrieve catalogs'));
      }
    );
  }

  public delete(loading: EventEmitter<boolean>, index: number) {
    const catalog = this.catalogs[index];
    loading.emit(true);
    this.catalogService.delete(catalog.id).subscribe(
      s => {
        this.catalogs.splice(index, 1);
        loading.emit(false);
        NotificationEmitter.emit(Notification.success('Delete successfully'));
      },
      error => {
        loading.emit(false);
        NotificationEmitter.emit(Notification.error(error.error.message, 'Unable to delete catalog with ID ' + catalog.id));
      }
    );
  }

  public update(loading: EventEmitter<boolean>, index: number, value: string) {
    const catalog = this.catalogs[index];
    loading.emit(true);
    this.catalogService.update(catalog.id, value).subscribe(
      s => {
        this.catalogs[index] = s;
        loading.emit(false);
        NotificationEmitter.emit(Notification.success('Update successfully'));
      },
      error => {
        loading.emit(false);
        NotificationEmitter.emit(Notification.error(error.error.message, 'Unable to delete update catalog with ID ' + catalog.id));
      }
    );
  }

  public createNew(loading: EventEmitter<boolean>, value: string) {
    loading.emit(true);
    this.catalogService.create(value).subscribe(
      s => {
        this.catalogs.push(s);
        this.createModalRef.close();
        NotificationEmitter.emit(Notification.success('Create successfully'));
        loading.emit(false);
      },
      error => {
        loading.emit(false);
        NotificationEmitter.emit(Notification.error(error.error.message, `Unable to create catalog "${value}"`));
      },
    );
  }

  ngOnInit() {
  }

}
