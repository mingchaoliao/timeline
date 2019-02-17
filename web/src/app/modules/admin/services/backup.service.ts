import {Injectable} from '@angular/core';
import {HttpService} from "../../core/shared/services/http.service";
import {Observable} from "rxjs";
import {Backup} from "../models/backup";
import {Url} from "../../core/shared/classes/url";

@Injectable()
export class BackupService {

    constructor(private httpService: HttpService) {
    }

    getAll(): Observable<Array<Backup>> {
        return new Observable<Array<Backup>>(observer => {
            this.httpService.get(Url.getAllBackup()).subscribe(
                responseBody => {
                    observer.next(Backup.fromArray(<Array<any>>responseBody));
                },
                error => {
                    observer.error(error);
                },
                () => {
                    observer.complete();
                }
            );
        });
    }
}
