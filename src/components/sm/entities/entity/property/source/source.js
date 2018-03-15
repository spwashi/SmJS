import Identity from "../../../../../identity/components/identity";

export class EntityPropertySource {
    _model;
    _context:{[name:string]: Identity};
    _property: Identity | null;
}