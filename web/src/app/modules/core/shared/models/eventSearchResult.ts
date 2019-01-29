import {EventDate} from './event';

export class Bucket {
  private readonly _value: string;
  private readonly _count: number;

  constructor(value: string, count: number) {
    this._value = value;
    this._count = count;
  }

  get value(): string {
    return this._value;
  }

  get count(): number {
    return this._count;
  }

  static fromJson(json: any): Bucket {
    if (json === null) {
      return null;
    }
    return new Bucket(
      json['value'],
      json['count']
    );
  }

  static fromArray(arr: Array<any>): Array<Bucket> {
    if (arr === null) {
      return [];
    }
    return arr.map((json: any) => {
      return Bucket.fromJson(json);
    });
  }
}

export class EventHit {
  private readonly _id: number;
  private readonly _startDate: EventDate;
  private readonly _startDateAttribute: string;
  private readonly _endDate: EventDate;
  private readonly _endDateAttribute: string;
  private readonly _content: string;

  constructor(id: number, startDate: EventDate, startDateAttribute: string, endDate: EventDate, endDateAttribute: string, content: string) {
    this._id = id;
    this._startDate = startDate;
    this._startDateAttribute = startDateAttribute;
    this._endDate = endDate;
    this._endDateAttribute = endDateAttribute;
    this._content = content;
  }

  get id(): number {
    return this._id;
  }

  get startDate(): EventDate {
    return this._startDate;
  }

  get startDateAttribute(): string {
    return this._startDateAttribute;
  }

  get endDate(): EventDate {
    return this._endDate;
  }

  get endDateAttribute(): string {
    return this._endDateAttribute;
  }

  get content(): string {
    return this._content;
  }

  static fromJson(json: any): EventHit {
    if (json === null) {
      return null;
    }
    return new EventHit(
      json['id'],
      EventDate.createFromString(json['startDate']),
      json['startDateAttribute'],
      EventDate.createFromString(json['endDate']),
      json['endDateAttribute'],
      json['content']
    );
  }

  static fromArray(arr: Array<any>): Array<EventHit> {
    if (arr === null) {
      return [];
    }
    return arr.map((json: any) => {
      return EventHit.fromJson(json);
    });
  }
}

export class EventSearchResult {
  private readonly _hits: Array<EventHit>;
  private readonly _periods: Array<Bucket>;
  private readonly _catalogs: Array<Bucket>;
  private readonly _dates: Array<Bucket>;
  private readonly _total: number;

  constructor(hits: Array<EventHit>, periods: Array<Bucket>, catalogs: Array<Bucket>, dates: Array<Bucket>, total: number) {
    this._hits = hits;
    this._periods = periods;
    this._catalogs = catalogs;
    this._dates = dates;
    this._total = total;
  }

  get hits(): Array<EventHit> {
    return this._hits;
  }

  get periods(): Array<Bucket> {
    return this._periods;
  }

  get catalogs(): Array<Bucket> {
    return this._catalogs;
  }

  get dates(): Array<Bucket> {
    return this._dates;
  }

  get total(): number {
    return this._total;
  }

  static fromJson(json: any, total: number): EventSearchResult {
    if (json === null) {
      return null;
    }
    return new EventSearchResult(
      EventHit.fromArray(json['hits']),
      Bucket.fromArray(json['periods']),
      Bucket.fromArray(json['catalogs']),
      Bucket.fromArray(json['dates']),
      total
    );
  }
}
