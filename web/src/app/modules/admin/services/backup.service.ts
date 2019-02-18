import {Injectable} from '@angular/core';
import {HttpService} from '../../core/shared/services/http.service';
import {Observable} from 'rxjs';
import {Backup} from '../models/backup';
import {Url} from '../../core/shared/classes/url';
import {map} from 'rxjs/operators';

@Injectable()
export class BackupService {

    constructor(private httpService: HttpService) {
    }

    getAll(): Observable<Array<Backup>> {
        return this.httpService.get(Url.getAllBackup()).pipe(
            map((responseBody: Array<any>) => Backup.fromArray(responseBody))
        );
    }

    download(name: string, password: string): Observable<Object> {
        return this.httpService.post(Url.downloadBackup(name), {}, {
            password: password
        }, {}, false, 'blob');
    }

    delete(name: string): Observable<boolean> {
        return this.httpService.delete(Url.deleteBackup(name)).pipe(
            map((responseBody: Object) => <boolean>responseBody)
        );
    }

    getSummary(): Observable<string> {
        return this.httpService.get(Url.getBackupSummary()).pipe(
            map((response: Object) => <string>response)
        );
    }

    getStatus() {
        return this.httpService.get(Url.getBackupStatus()).pipe(
            map((response: Object) => <string>response)
        );
    }

    create(): Observable<Object> {
        return this.httpService.post(Url.createBackup());
    }
}
