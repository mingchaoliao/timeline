export class Typeahead {
    private readonly _id: number;
    private readonly _value: string;

    constructor(id: number, value: string) {
        this._id = id;
        this._value = value;
    }

    get id(): number {
        return this._id;
    }

    get value(): string {
        return this._value;
    }

    static fromJson(json: any): Typeahead {
        if (json === null) {
            return null;
        }
        return new Typeahead(
            json['id'],
            json['value']
        );
    }

    static fromArray(arr: Array<any>): Array<Typeahead> {
        if (arr === null) {
            return [];
        }
        return arr.map((json: any) => {
            return Typeahead.fromJson(json);
        });
    }
}