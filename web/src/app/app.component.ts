import {Component} from '@angular/core';
import {UserService} from './modules/core/shared/services/user.service';
import * as moment from 'moment';
import {environment} from '../environments/environment';
import {ActivatedRoute, Router} from '@angular/router';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent {
  public title = environment.title;
  public currentYear: string;
  public company: any;

  constructor(private userService: UserService,
              private router: Router) {
    this.userService.getCurrentUser().subscribe(
      user => {
      },
      error => {
      }
    );

    this.currentYear = moment().format('YYYY');
    this.company = environment.company;
  }

  getUser() {
    return UserService.getCurrentUser();
  }

  isRouteActive(path: string): boolean {
    const mathes = this.router.url.match(/(.+?(?=\?)|^[/\d\w\-_]+$)/);
    return mathes && mathes[1] === path;
  }

  public logout() {
    UserService.logout();
  }
}
