import {DateAttribute} from './dateAttribute';
import {Period} from './period';
import {Catalog} from './catalog';
import {Image} from './image';

export class EventDate {
  private readonly _date: Date;
  private readonly _hasMonth: boolean;
  private readonly _hasDay: boolean;

  constructor(date: Date, hasMonth: boolean, hasDay: boolean) {
    this._date = date;
    this._hasMonth = hasMonth;
    this._hasDay = hasDay;
  }

  get date(): Date {
    return this._date;
  }

  get hasMonth(): boolean {
    return this._hasMonth;
  }

  get hasDay(): boolean {
    return this._hasDay;
  }

  static fromJson(json: any): EventDate {
    if (json === null) {
      return null;
    }
    return new EventDate(
      json['date'],
      json['hasMonth'],
      json['hasDay']
    );
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
      EventDate.fromJson(json['startDate']),
      EventDate.fromJson(json['endDate']),
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
