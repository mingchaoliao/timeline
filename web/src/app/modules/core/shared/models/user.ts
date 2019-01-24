export class User {
  protected readonly _id: number;
  protected readonly _name: string;
  protected readonly _email: string;
  private _isAdmin: boolean;
  private _isEditor: boolean;
  protected readonly _createdAt: Date;
  protected readonly _updatedAt: Date;

  constructor(
    id: number,
    name: string,
    email: string,
    isAdmin: boolean,
    isEditor: boolean,
    createdAt: Date,
    updatedAt: Date
  ) {
    this._id = id;
    this._name = name;
    this._email = email;
    this._isAdmin = isAdmin;
    this._isEditor = isEditor;
    this._createdAt = createdAt;
    this._updatedAt = updatedAt;
  }

  get id(): number {
    return this._id;
  }

  get name(): string {
    return this._name;
  }

  get email(): string {
    return this._email;
  }

  get isAdmin(): boolean {
    return this._isAdmin;
  }

  set isAdmin(value: boolean) {
    this._isAdmin = value;
  }

  get isEditor(): boolean {
    return this._isEditor;
  }

  set isEditor(value: boolean) {
    this._isEditor = value;
  }

  get createdAt(): Date {
    return this._createdAt;
  }

  get updatedAt(): Date {
    return this._updatedAt;
  }

  static fromJson(json: any): User {
    if (json === null) {
      return null;
    }
    return new User(
      json['id'],
      json['name'],
      json['email'],
      json['isAdmin'],
      json['isEditor'],
      json['createdAt'],
      json['updatedAt']
    );
  }

  static fromArray(arr: Array<any>): Array<User> {
    if (arr === null) {
      return [];
    }
    return arr.map((json: any) => {
      return User.fromJson(json);
    });
  }
}
