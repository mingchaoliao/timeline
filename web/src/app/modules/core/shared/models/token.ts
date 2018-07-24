import {JwtHelperService} from '@auth0/angular-jwt';

export enum TokenType {
  Bearer = 'Bearer'
}

export class Token {
  private jwtHelper: JwtHelperService;

  constructor(token: string, type: TokenType) {
    this._token = token;
    this.jwtHelper = new JwtHelperService();
    this._expiredAt = this.jwtHelper.getTokenExpirationDate(token);
  }

  private _token: string;

  get token(): string {
    return this._token;
  }

  private _type: TokenType;

  get type(): TokenType {
    return this._type;
  }

  private _expiredAt: Date;

  get expiredAt(): Date {
    return this._expiredAt;
  }

  public isTokenExpired(): boolean {
    return this.jwtHelper.isTokenExpired(this.token);
  }
}
