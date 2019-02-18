import {Component, ElementRef, EventEmitter, OnInit, Output, ViewChild} from '@angular/core';
import {NgbModal, NgbModalRef} from "@ng-bootstrap/ng-bootstrap";
import {Backup} from "../../models/backup";

@Component({
  selector: 'app-backup-delete-confirmation-modal',
  templateUrl: './backup-delete-confirmation-modal.component.html',
  styleUrls: ['./backup-delete-confirmation-modal.component.css']
})
export class BackupDeleteConfirmationModalComponent implements OnInit {
  @ViewChild('deleteConfirmationModal') private _deleteConfirmationModal: ElementRef;
  private _deleteConfirmationModalRef: NgbModalRef;
  private _backup: Backup;
  @Output('onDelete') _onDelete: EventEmitter<Backup> = new EventEmitter<Backup>();

  constructor(private _modalService: NgbModal) {
  }

  open(backup: Backup) {
    this._backup = backup;
    this._deleteConfirmationModalRef = this._modalService.open(this._deleteConfirmationModal);
  }

  delete() {
    this._onDelete.emit(this._backup);
    this._backup = null;
    this._deleteConfirmationModalRef.close();
  }

  ngOnInit() {
  }

}
