export class Period {
  private readonly _id: number;
  private readonly _value: string;
  private readonly _createUserId: number;
  private readonly _updateUserId: number;
  private readonly _createdAt: Date;
  private readonly _updatedAt: Date;

  constructor(id: number, value: string, createUserId: number, updateUserId: number, createdAt: Date, updatedAt: Date) {
    this._id = id;
    this._value = value;
    this._createUserId = createUserId;
    this._updateUserId = updateUserId;
    this._createdAt = createdAt;
    this._updatedAt = updatedAt;
  }

  get id(): number {
    return this._id;
  }

  get value(): string {
    return this._value;
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

  static fromJson(json: any): Period {
    if (json === null) {
      return null;
    }
    return new Period(
      json['id'],
      json['value'],
      json['createUserId'],
      json['updateUserId'],
      json['createdAt'],
      json['updatedAt']
    );
  }

  static fromArray(arr: Array<any>): Array<Period> {
    if (arr === null) {
      return [];
    }
    return arr.map((json: any) => {
      return Period.fromJson(json);
    });
  }
}