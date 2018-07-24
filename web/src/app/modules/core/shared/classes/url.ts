import {environment} from '../../../../../environments/environment';

export class Url {
  public static login(): string {
    return environment.wsRoot + '/login';
  }

  public static getCurrentUser(): string {
    return environment.wsRoot + '/user';
  }

  static register() {
    return environment.wsRoot + '/register';
  }

  static getEvents(): string {
    return environment.wsRoot + '/event';
  }

  static getImageByPath(path: string, withAdminAccess: boolean = false): string {
    if (withAdminAccess) {
      return environment.wsRoot + '/admin/image/' + path;
    }

    return environment.wsRoot + '/image/' + path;
  }

  static getEventById(id: number): string {
    return environment.wsRoot + '/event/' + id;
  }

  static getPeriod(): string {
    return environment.wsRoot + '/period';
  }

  static createPeriod(): string {
    return environment.wsRoot + '/period';
  }

  static bulkCreatePeriod(): string {
    return environment.wsRoot + '/period/bulkCreate';
  }

  static getCatalog(): string {
    return environment.wsRoot + '/catalog';
  }

  static createCatalog(): string {
    return environment.wsRoot + '/catalog';
  }

  static bulkCreateCatalog(): string {
    return environment.wsRoot + '/catalog/bulkCreate';
  }

  static getDateAttribute(): string {
    return environment.wsRoot + '/dateAttribute';
  }

  static createDateAttribute(): string {
    return environment.wsRoot + '/dateAttribute';
  }

  static bulkCreateDateAttribute(): string {
    return environment.wsRoot + '/dateAttribute/bulkCreate';
  }

  static getDateFormat(): string {
    return environment.wsRoot + '/dateFormat';
  }

  static getTimeline(): string {
    return environment.wsRoot + '/storage/timeline.json';
  }

  static createEvent(): string {
    return environment.wsRoot + '/event';
  }

  static updateEventById(id: number): string {
    return environment.wsRoot + '/event/' + id;
  }

  static bulkCreateEvents(): string {
    return environment.wsRoot + '/event/bulkCreate';
  }
}
