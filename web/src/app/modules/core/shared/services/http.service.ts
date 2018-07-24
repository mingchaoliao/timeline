import {Injectable} from '@angular/core';
import {HttpClient, HttpHeaders} from '@angular/common/http';
import {Common} from '../utils/common.class';
import {JwtHelperService} from '@auth0/angular-jwt';

@Injectable()
export class HttpService {
  private jwtHelper: JwtHelperService;

  constructor(public http: HttpClient) {
    this.jwtHelper = new JwtHelperService();
  }

  get(url: string, queryParameters: any = {}, headers: any = {}, fullResponse: boolean = false) {
    const options = {
      headers: this.createHeaders(headers)
    };
    if (fullResponse === true) {
      options['observe'] = 'response';
    }
    return this.http.get(
      url + Common.buildQueryParameters(queryParameters),
      options
    );
  }

  post(
    url: string,
    queryParameters: any = {},
    requestBody: any = {},
    headers: any = {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    },
    fullResponse: boolean = false
  ) {
    const options = {
      headers: this.createHeaders(headers)
    };
    if (fullResponse === true) {
      options['observe'] = 'response';
    }
    return this.http.post(
      url + Common.buildQueryParameters(queryParameters),
      requestBody,
      options
    );
  }

  put(
    url: string,
    queryParameters: any = {},
    requestBody: any = {},
    headers: any = {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    },
    fullResponse: boolean = false
  ) {
    const options = {
      headers: this.createHeaders(headers)
    };
    if (fullResponse === true) {
      options['observe'] = 'response';
    }
    return this.http.put(
      url + Common.buildQueryParameters(queryParameters),
      requestBody,
      options
    );
  }

  delete(url: string, queryParameters: any = {}, headers: any = {}, fullResponse: boolean = false) {
    const options = {
      headers: this.createHeaders(headers)
    };
    if (fullResponse === true) {
      options['observe'] = 'response';
    }
    return this.http.delete(
      url + Common.buildQueryParameters(queryParameters),
      options
    );
  }

  private createHeaders(headers: any = {}): HttpHeaders {
    return new HttpHeaders(headers);
  }
}
