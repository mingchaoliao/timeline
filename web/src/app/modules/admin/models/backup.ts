export class Backup {
    private readonly _name: string;
    private readonly _size: number;
    private readonly _date: Date;

    constructor(name: string, size: number, date: Date) {
        this._name = name;
        this._size = size;
        this._date = date;
    }

    get name(): string {
        return this._name;
    }

    get size(): number {
        return this._size;
    }

    get date(): Date {
        return this._date;
    }

    static fromJson(json: any): Backup {
        if (json === null) {
            return null;
        }
        return new Backup(
            json['name'],
            json['size'],
            json['date']
        );
    }

    static fromArray(arr: Array<any>): Array<Backup> {
        if (arr === null) {
            return [];
        }
        return arr.map((json: any) => {
            return Backup.fromJson(json);
        });
    }
}