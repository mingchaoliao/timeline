export enum NotificationType {
  SUCCESS,
  INFO,
  WARNING,
  ERROR
}

export class Notification {
  private readonly _message: string;
  private readonly _timeout: number;
  private readonly _isDismissible: boolean;
  private readonly _type: NotificationType;
  private _isTimeout = false;

  constructor(message: string, timeout: number, isDismissible: boolean, type: NotificationType) {
    this._message = message;
    this._timeout = timeout;
    this._isDismissible = isDismissible;
    this._type = type;
  }

  static info(message: string, altMessage: string = '', timeout: number = 2, isDismissible: boolean = true): Notification {
    return new Notification(message === undefined ? altMessage : message, timeout, isDismissible, NotificationType.INFO);
  }

  static success(message: string, altMessage: string = '', timeout: number = 2, isDismissible: boolean = true): Notification {
    return new Notification(message === undefined ? altMessage : message, timeout, isDismissible, NotificationType.SUCCESS);
  }

  static warning(message: string, altMessage: string = '', timeout: number = 2, isDismissible: boolean = true): Notification {
    return new Notification(message === undefined ? altMessage : message, timeout, isDismissible, NotificationType.WARNING);
  }

  static error(message: string, altMessage: string = '', timeout: number = 2, isDismissible: boolean = true): Notification {
    return new Notification(message === undefined ? altMessage : message, timeout, isDismissible, NotificationType.ERROR);
  }

  get message(): string {
    return this._message;
  }

  get timeout(): number {
    return this._timeout;
  }

  get isDismissible(): boolean {
    return this._isDismissible;
  }

  get type(): string {
    switch (this._type) {
      case NotificationType.INFO:
        return 'info';
      case NotificationType.SUCCESS:
        return 'success';
      case NotificationType.WARNING:
        return 'warning';
      case NotificationType.ERROR:
        return 'danger';
    }
  }

  set isTimeout(value: boolean) {
    this._isTimeout = value;
  }

  get isTimeout(): boolean {
    return this._isTimeout;
  }
}
