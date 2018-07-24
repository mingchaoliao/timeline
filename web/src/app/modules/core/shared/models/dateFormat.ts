export class DateFormat {
  private readonly _id: number;
  private readonly _value: string;
  private readonly _isAttributeAllowed: boolean;
  private readonly _createdAt: Date;
  private readonly _updatedAt: Date;

  constructor(id: number, value: string, isAttributeAllowed: boolean, createdAt: Date, updatedAt: Date) {
    this._id = id;
    this._value = value;
    this._isAttributeAllowed = isAttributeAllowed;
    this._createdAt = createdAt;
    this._updatedAt = updatedAt;
  }

  get id(): number {
    return this._id;
  }

  get value(): string {
    return this._value;
  }

  get isAttributeAllowed(): boolean {
    return this._isAttributeAllowed;
  }

  get createdAt(): Date {
    return this._createdAt;
  }

  get updatedAt(): Date {
    return this._updatedAt;
  }

  static fromJson(json: any): DateFormat {
    if (json === null) {
      return null;
    }
    return new DateFormat(
      json['id'],
      json['mysqlFormat'],
      json['isAttributeAllowed'],
      json['createdAt'],
      json['updatedAt']
    );
  }

  static fromArray(arr: Array<any>): Array<DateFormat> {
    if (arr === null) {
      return [];
    }
    return arr.map((json: any) => {
      return DateFormat.fromJson(json);
    });
  }
}