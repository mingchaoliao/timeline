import {EventEmitter} from '@angular/core';
import {Language} from "../models/language";

export class LanguageEmitter {
  private static _emitter: EventEmitter<Language> = new EventEmitter<Language>();
  private static _currentLanguage: Language;

  public static emit(language: Language) {
    this._emitter.emit(language);
    this._currentLanguage = language;
  }

  static get currentLanguage(): Language {
    return this._currentLanguage;
  }

  static get emitter(): EventEmitter<Language> {
    return this._emitter;
  }
}
