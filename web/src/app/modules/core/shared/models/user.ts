import {Token, TokenType} from './token';

export class User {
  private readonly _id: number;
  private readonly _name: string;
  private readonly _email: string;
  private readonly _isAdmin: boolean;
  private readonly _accessToken: Token;
  private readonly _createdAt: Date;
  private readonly _updatedAt: Date;

  constructor(
    id: number,
    name: string,
    email: string,
    isAdmin: boolean,
    accessToken: Token,
    createdAt: Date,
    updatedAt: Date
  ) {
    this._id = id;
    this._name = name;
    this._email = email;
    this._isAdmin = isAdmin;
    this._accessToken = accessToken;
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

  get accessToken(): Token {
    return this._accessToken;
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
      new Token(json['accessToken'], TokenType.Bearer),
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
