///<reference path="../../../../node_modules/@angular/core/src/metadata/lifecycle_hooks.d.ts"/>
import {Component, HostListener, OnInit} from '@angular/core';
import {EventService} from '../core/shared/services/event.service';
import {Language} from '../core/shared/models/language';
import {LanguageEmitter} from '../core/shared/events/languageEmitter';
import {DomSanitizer} from '@angular/platform-browser';
import {Url} from "../core/shared/classes/url";

@Component({
    selector: 'app-home',
    templateUrl: './home.component.html',
    styleUrls: ['./home.component.css']
})
export class HomeComponent implements OnInit {
    private _hits = [];
    private _language: Language;
    private _isLoading = true;
    private _currentPage = 0;
    private _maxPage = null;

    get sanitizer(): DomSanitizer {
        return this._sanitizer;
    }

    get hits(): any[] {
        return this._hits;
    }

    get language(): Language {
        return this._language;
    }

    constructor(private _eventService: EventService, private _sanitizer: DomSanitizer) {

    }

    @HostListener('window:beforeunload', [])
    beforeunload() {
        window.scrollTo(0, 0);
    }

    @HostListener('window:scroll', [])
    onWindowScroll() {
        if (this._isLoading || (this._maxPage && this._currentPage >= this._maxPage)) {
            return;
        }
        const scrollPosition = window.pageYOffset;
        const windowSize = window.innerHeight;
        const bodyHeight = document.body.offsetHeight;
        const distanceToBottom = Math.max(bodyHeight - (scrollPosition + windowSize), 0);
        if (distanceToBottom === 0) {
            this.loadData();
        }
    }

    getImageUrl(image): string {
        if (image['id']) {
            return Url.getTempImage(image.path);
        } else {
            return Url.getImage(image);
        }
    }

    loadData() {
        this._isLoading = true;
        this._eventService.search(null, null, null, null, this._currentPage + 1, 20).subscribe(
            result => {
                this._hits = this._hits.concat(result.hits);
                this._isLoading = false;
                this._currentPage++;
                this._maxPage = Math.ceil(result.total / 20.0);
            },
            e => {

            }
        );
    }

    ngOnInit(): void {
        this._language = LanguageEmitter.currentLanguage;
        LanguageEmitter.emitter.subscribe((language: Language) => this._language = language);
        this.loadData();
    }
}
