import {EventEmitter} from '@angular/core';

export class AuthEmitter {
  private static _emitter: EventEmitter<boolean> = new EventEmitter<boolean>();

  public static emit(authenticated: boolean) {
    this._emitter.emit(authenticated);
  }

  static get emitter(): EventEmitter<boolean> {
    return this._emitter;
  }
}
