import {Component, OnInit} from '@angular/core';
import {BackupService} from "../../services/backup.service";
import {Observable} from "rxjs";
import {Backup} from "../../models/backup";

@Component({
  selector: 'app-backup',
  templateUrl: './backup.component.html',
  styleUrls: ['./backup.component.css']
})
export class BackupComponent implements OnInit {
  private _backups: Observable<Array<Backup>>;

  constructor(private backupService: BackupService) {
  }

  ngOnInit() {
    this._backups = this.backupService.getAll();
  }

  get backups(): Observable<Array<Backup>> {
    return this._backups;
  }
}
