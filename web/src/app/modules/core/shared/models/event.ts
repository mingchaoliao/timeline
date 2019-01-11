import {DateAttribute} from './dateAttribute';
import {DateFormat} from './dateFormat';
import {Period} from './period';
import {Catalog} from './catalog';
import {Image} from './image';

export class Event {
  private readonly _id: number;
  private readonly _startDate: Date;
  private readonly _endDate: Date;
  private readonly _startDateAttribute: DateAttribute;
  private readonly _endDateAttribute: DateAttribute;
  private readonly _startDateFormat: DateFormat;
  private readonly _endDateFormat: DateFormat;
  private readonly _period: Period;
  private readonly _catalogs: Array<Catalog>;
  private readonly _content: string;
  private readonly _images: Array<Image>;
  private readonly _createUserId: number;
  private readonly _updateUserId: number;
  private readonly _createdAt: Date;
  private readonly _updatedAt: Date;

  constructor(id: number, startDate: Date, endDate: Date, startDateAttribute: DateAttribute, endDateAttribute: DateAttribute,
              startDateFormat: DateFormat, endDateFormat: DateFormat, period: Period, catalogs: Array<Catalog>, content: string,
              images: Array<Image>, createUserId: number, updateUserId: number, createdAt: Date, updatedAt: Date) {
    this._id = id;
    this._startDate = startDate;
    this._endDate = endDate;
    this._startDateAttribute = startDateAttribute;
    this._endDateAttribute = endDateAttribute;
    this._startDateFormat = startDateFormat;
    this._endDateFormat = endDateFormat;
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

  get startDate(): Date {
    return this._startDate;
  }

  get endDate(): Date {
    return this._endDate;
  }

  get startDateAttribute(): DateAttribute {
    return this._startDateAttribute;
  }

  get endDateAttribute(): DateAttribute {
    return this._endDateAttribute;
  }

  get startDateFormat(): DateFormat {
    return this._startDateFormat;
  }

  get endDateFormat(): DateFormat {
    return this._endDateFormat;
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
      json['startDate'],
      json['endDate'],
      DateAttribute.fromJson(json['startDateAttribute']),
      DateAttribute.fromJson(json['endDateAttribute']),
      DateFormat.fromJson(json['startDateFormat']),
      DateFormat.fromJson(json['endDateFormat']),
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
