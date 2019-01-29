import {CanActivate} from '@angular/router';
import {Injectable} from '@angular/core';
import {UserService} from './modules/core/shared/services/user.service';
import {Observable} from 'rxjs';

@Injectable()
export class EditorGuard implements CanActivate {
  constructor(private userService: UserService) {

  }

  canActivate() {
    return new Observable<boolean>(
      observer => {
        this.userService.getCurrentUser().subscribe(
          user => {
            if (user.isEditor || user.isAdmin) {
              observer.next(true);
            } else {
              observer.next(false);
            }
          },
          error => {
            observer.next(false);
          },
          () => {
            observer.complete();
          }
        );
      }
    );
  }

}
