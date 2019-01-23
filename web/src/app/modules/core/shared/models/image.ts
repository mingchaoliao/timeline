export class Image {
  private readonly _id: number;
  private readonly _path: string;
  private readonly _description: string;
  private readonly _eventId: number;
  private readonly _createUserId: number;
  private readonly _updateUserId: number;
  private readonly _createdAt: Date;
  private readonly _updatedAt: Date;

  constructor(id: number, path: string, description: string, eventId: number, createUserId: number, updateUserId: number, createdAt: Date, updatedAt: Date) {
    this._id = id;
    this._path = path;
    this._description = description;
    this._eventId = eventId;
    this._createUserId = createUserId;
    this._updateUserId = updateUserId;
    this._createdAt = createdAt;
    this._updatedAt = updatedAt;
  }

  get eventId(): number {
    return this._eventId;
  }

  get id(): number {
    return this._id;
  }

  get path(): string {
    return this._path;
  }

  get description(): string {
    return this._description;
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

  static fromJson(json: any): Image {
    if (json === null) {
      return null;
    }
    return new Image(
      json['id'],
      json['path'],
      json['description'],
      json['eventId'],
      json['createUserId'],
      json['updateUserId'],
      json['createdAt'],
      json['updatedAt']
    );
  }

  static fromArray(arr: Array<any>): Array<Image> {
    if (arr === null) {
      return [];
    }
    return arr.map((json: any) => {
      return Image.fromJson(json);
    });
  }
}
