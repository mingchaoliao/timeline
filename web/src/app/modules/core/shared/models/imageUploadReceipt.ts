export class ImageUploadReceipt {
  private readonly _path: string;
  private readonly _originalName: string;

  constructor(path: string, originalName: string) {
    this._path = path;
    this._originalName = originalName;
  }

  get path(): string {
    return this._path;
  }

  get originalName(): string {
    return this._originalName;
  }

  static fromJson(json: any): ImageUploadReceipt {
    return new ImageUploadReceipt(
      json['path'],
      json['originalName']
    );
  }

  static fromArray(arr: Array<any>): Array<ImageUploadReceipt> {
    return arr.map((json: any) => ImageUploadReceipt.fromJson(json));
  }
}