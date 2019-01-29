import {DateAttribute} from './dateAttribute';
import {Period} from './period';
import {Catalog} from './catalog';
import {Image} from './image';
import {NgbDateStruct} from '@ng-bootstrap/ng-bootstrap';
import * as moment from 'moment';

export class EventDate {
  public static readonly FORMAT_YEAR = 'YYYY';
  public static readonly FORMAT_YEAR_MONTH = 'YYYY-MM';
  public static readonly FORMAT_YEAR_MONTH_DAY = 'YYYY-MM-DD';
  public static readonly REGEXR_YEAR = /^[0-9]{4}$/;
  public static readonly REGEXR_YEAR_MONTH = /^([0-9]{4})-(0[1-9]|1[0-2])$/;
  public static readonly REGEXR_YEAR_MONTH_DAY = /^([0-9]{4})-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/;

  private readonly _date: string;
  private readonly _format: string;

  public static validate(str: string): boolean {
    return this.getFormat(str) !== null;
  }

  public static getFormat(str: string): string {
    if (str.match(this.REGEXR_YEAR)) {
      return this.FORMAT_YEAR;
    } else if (str.match(this.REGEXR_YEAR_MONTH)) {
      return this.FORMAT_YEAR_MONTH;
    } else if (str.match(this.REGEXR_YEAR_MONTH_DAY)) {
      return this.FORMAT_YEAR_MONTH_DAY;
    }
    return null;
  }

  constructor(date: string) {
    this._date = date;
    const format = EventDate.getFormat(date);
    if (!format) {
      throw new Error(`Invalid event date ${date}`);
    }
    this._format = format;
  }

  get date(): string {
    return this._date;
  }

  static createFromString(str: string) {
    if (!str) {
      return null;
    }

    return new EventDate(str);
  }

  public isAttributeAllowed(): boolean {
    return this._format === EventDate.FORMAT_YEAR;
  }

  public toNgbDate(): NgbDateStruct {
    const date = moment(this._date, this._format);
    return {
      year: date.year(),
      month: date.month() + 1,
      day: date.date()
    };
  }
}

export class Event {
  private readonly _id: number;
  private readonly _startDate: EventDate;
  private readonly _endDate: EventDate;
  private readonly _startDateAttribute: DateAttribute;
  private readonly _endDateAttribute: DateAttribute;
  private readonly _period: Period;
  private readonly _catalogs: Array<Catalog>;
  private readonly _content: string;
  private readonly _images: Array<Image>;
  private readonly _createUserId: number;
  private readonly _updateUserId: number;
  private readonly _createdAt: Date;
  private readonly _updatedAt: Date;

  constructor(id: number, startDate: EventDate, endDate: EventDate, startDateAttribute: DateAttribute, endDateAttribute: DateAttribute, period: Period, catalogs: Array<Catalog>, content: string, images: Array<Image>, createUserId: number, updateUserId: number, createdAt: Date, updatedAt: Date) {
    this._id = id;
    this._startDate = startDate;
    this._endDate = endDate;
    this._startDateAttribute = startDateAttribute;
    this._endDateAttribute = endDateAttribute;
    this._period = period;
    this._catalogs = catalogs;
    this._content = content;
    this._images = images;
    this._createUserId = createUserId;
    this._updateUserId = updateUserId;
    this._createdAt = createdAt;
    this._updatedAt = updatedAt;
  }

  get id(): number {
    return this._id;
  }

  get startDate(): EventDate {
    return this._startDate;
  }

  get endDate(): EventDate {
    return this._endDate;
  }

  get startDateAttribute(): DateAttribute {
    return this._startDateAttribute;
  }

  get endDateAttribute(): DateAttribute {
    return this._endDateAttribute;
  }

  get period(): Period {
    return this._period;
  }

  get catalogs(): Array<Catalog> {
    return this._catalogs;
  }

  get content(): string {
    return this._content;
  }

  get images(): Array<Image> {
    return this._images;
  }

  get createUserId(): number {
    return this._createUserId;
  }

  get updateUserId(): number {
    return this._updateUserId;
  }

  get createdAt(): Date {
    return this._createdAt;
  }

  get updatedAt(): Date {
    return this._updatedAt;
  }

  static fromJson(json: any): Event {
    if (json === null) {
      return null;
    }
    return new Event(
      json['id'],
      EventDate.createFromString(json['startDate']),
      EventDate.createFromString(json['endDate']),
      DateAttribute.fromJson(json['startDateAttribute']),
      DateAttribute.fromJson(json['endDateAttribute']),
      Period.fromJson(json['period']),
      Catalog.fromArray(json['catalogCollection']),
      json['content'],
      Image.fromArray(json['imageCollection']),
      json['createUserId'],
      json['updateUserId'],
      json['createdAt'],
      json['updatedAt']
    );
  }

  static fromArray(arr: Array<any>): Array<Event> {
    if (arr === null) {
      return [];
    }
    return arr.map((json: any) => {
      return Event.fromJson(json);
    });
  }
}
