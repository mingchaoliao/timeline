import {Component, OnInit} from '@angular/core';
import {UserService} from './modules/core/shared/services/user.service';
import * as moment from 'moment';
import {environment} from '../environments/environment';
import {Router} from '@angular/router';
import {AuthEmitter} from './modules/core/shared/events/authEmitter';
import {TranslateService} from '@ngx-translate/core';
import {Language} from './modules/core/shared/models/language';
import {LanguageEmitter} from './modules/core/shared/events/languageEmitter';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent implements OnInit {
  public title = environment.title;
  public currentYear: string;
  public company: any;
  private _currentLanguage: Language;
  private _languages: Array<Language> = environment.languages;

  constructor(private userService: UserService,
              private router: Router,
              private _translateService: TranslateService) {
    this.userService.getCurrentUser().subscribe(
        user => {
        },
        error => {
        }
    );

    AuthEmitter.emitter.subscribe(
        authenticated => {
          if (!authenticated) {
            UserService.logout();
            this.router.navigate(['/', 'account', 'login']);
          }
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

  get currentLanguage(): Language {
    return this._currentLanguage;
  }

  get languages(): Array<Language> {
    return this._languages;
  }

  public logout() {
    UserService.logout();
  }

  ngOnInit(): void {
    const localStorageLanguageStr = localStorage.getItem('language');
    const defaultLanguage = this._languages.find((language: Language) => language.default);
    this._translateService.setDefaultLang(defaultLanguage.cultureLang);
    if (localStorageLanguageStr) {
      this._currentLanguage = JSON.parse(localStorageLanguageStr);
      this._translateService.use(this._currentLanguage.cultureLang);
    } else {
      const browserCultureLang = this._translateService.getBrowserCultureLang().toLowerCase();
      this._currentLanguage = this._languages.find((language: Language) => language.cultureLang === browserCultureLang);
      if (this._currentLanguage) {
        this._translateService.use(this._currentLanguage.cultureLang);
      } else {
        this._currentLanguage = defaultLanguage;
      }
    }
    LanguageEmitter.emit(this._currentLanguage);
  }

  onLanguageChange(language: Language): void {
    this._currentLanguage = language;
    this._translateService.use(this._currentLanguage.cultureLang);
    localStorage.setItem('language', JSON.stringify(this._currentLanguage));
    LanguageEmitter.emit(this._currentLanguage);
  }
}
