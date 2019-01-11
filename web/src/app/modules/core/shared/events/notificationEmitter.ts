import {EventEmitter} from '@angular/core';
import {Notification} from '../models/notification';

export class NotificationEmitter {
  private static _emitter: EventEmitter<Notification> = new EventEmitter<Notification>();

  public static emit(notification: Notification) {
    NotificationEmitter._emitter.emit(notification);
  }

  static get emitter(): EventEmitter<Notification> {
    return this._emitter;
  }
}
