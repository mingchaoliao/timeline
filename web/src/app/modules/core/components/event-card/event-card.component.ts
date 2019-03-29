import {ChangeDetectionStrategy, ChangeDetectorRef, Component, Input, OnInit, ViewEncapsulation} from '@angular/core';
import {DomSanitizer} from '@angular/platform-browser';
import {CommonService} from '../../shared/services/common.service';
import {UserService} from '../../shared/services/user.service';
import {User} from '../../shared/models/user';
import {Url} from '../../shared/classes/url';
import {EventService} from '../../shared/services/event.service';
import {Notification} from '../../shared/models/notification';
import {NotificationEmitter} from '../../shared/events/notificationEmitter';
import {LanguageEmitter} from "../../shared/events/languageEmitter";
import {Language} from "../../shared/models/language";

@Component({
  selector: 'app-event-card',
  templateUrl: './event-card.component.html',
  styleUrls: ['./event-card.component.css'],
  encapsulation: ViewEncapsulation.None,
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class EventCardComponent implements OnInit {

  @Input() hit;
  @Input() isPreview = false;
  private _isDetailView = false;
  public isDeleted = false;
  private _language: Language;

  constructor(
      public sanitizer: DomSanitizer,
      public common: CommonService,
      private cdr: ChangeDetectorRef,
      private eventService: EventService) {

  }

  getImageUrl(image): string {
    if (image['id']) {
      return Url.getTempImage(image.path);
    } else {
      return Url.getImage(image);
    }
  }

  public getUser(): User {
    return UserService.getCurrentUser();
  }

  ngOnInit() {
    this._language = LanguageEmitter.currentLanguage;
    LanguageEmitter.emitter.subscribe((language: Language) => this._language = language);
  }

  get language(): Language {
    return this._language;
  }

  get isDetailView(): boolean {
    return this._isDetailView;
  }

  set isDetailView(value: boolean) {
    this._isDetailView = value;
  }

  public onDelete(id: number) {
    this.eventService.deleteById(id).subscribe(
        success => {
          this.isDeleted = true;
          NotificationEmitter.emit(Notification.success('Event delete successfully.'));
          this.cdr.detectChanges();
        },
        error => {
          NotificationEmitter.emit(Notification.error(error.error.message));
          this.cdr.detectChanges();
        }
    );
  }
}
