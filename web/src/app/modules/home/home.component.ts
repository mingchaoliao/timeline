///<reference path="../../../../node_modules/@angular/core/src/metadata/lifecycle_hooks.d.ts"/>
import {AfterViewInit, Component, OnDestroy, OnInit} from '@angular/core';
import {TimelineService} from '../core/shared/services/timeline.service';
import {Notification} from '../core/shared/models/notification';
import {NotificationEmitter} from '../core/shared/events/notificationEmitter';
import {LanguageEmitter} from "../core/shared/events/languageEmitter";
import {Language} from "../core/shared/models/language";
import {Subscription} from "rxjs";

declare var TL: any;

@Component({
    selector: 'app-home',
    templateUrl: './home.component.html',
    styleUrls: ['./home.component.css']
})
export class HomeComponent implements OnInit, AfterViewInit, OnDestroy {
    private _languageEmitterSubscription: Subscription;
    private _timelineDataSubscription: Subscription;
    private _timelineData: any;

    constructor(
        private timelineService: TimelineService
    ) {

    }

    ngOnInit() {
    }

    ngAfterViewInit(): void {
        this.getTimeline(LanguageEmitter.currentLanguage);
        this._languageEmitterSubscription = LanguageEmitter.emitter.subscribe((language: Language) => {
            this.getTimeline(language);
        });
    }

    getTimeline(language: Language) {
        if (this._timelineData) {
            this.bootstrapTimeline(this._timelineData, language);
        } else {
            this._timelineDataSubscription = this.timelineService.get().subscribe(
                timeline => {
                    this._timelineData = timeline;
                    this.bootstrapTimeline(this._timelineData, language);
                },
                error => {
                    console.log(error);
                    NotificationEmitter.emit(Notification.error(error.error.message, 'Unable to retrieve events'));
                }
            );
        }
    }

    bootstrapTimeline(data: any, language: Language) {
        const cultureLang = language.lang === 'en' ? 'en' : language.cultureLang;

        new TL.Timeline('timeline', data, {
            language: cultureLang,
            hash_bookmark: true,
            script_path: 'assets/timeline'
        });
    }

    ngOnDestroy(): void {
        if (this._timelineDataSubscription) {
            this._timelineDataSubscription.unsubscribe();
        }

        if (this._languageEmitterSubscription) {
            this._languageEmitterSubscription.unsubscribe();
        }
    }
}
