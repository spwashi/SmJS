/**
 * Created by Sam Washington on 5/6/17.
 */
export default class JsonMLElement {
    /**
     * @return {JsonMLElement}
     */
    static init() { return new this(...arguments); }
    
    constructor(tagName) {
        this._attributes = {};
        this._jsonML     = [tagName, this._attributes];
    }
    
    createChild(tagName, style) {
        const c =
                  tagName instanceof JsonMLElement
                      ? tagName
                      : new JsonMLElement(tagName);
        if (style) c.setStyle(style);
        this._jsonML.push(c.toJsonML());
        return c;
    }
    
    createObjectTag(object, style) {
        return this.createChild("object", style)
                   .addAttribute("object", object);
    }
    
    setStyle(style) {
        this._attributes["style"] = style;
        return this;
    }
    
    addAttribute(key, value) {
        this._attributes[key] = value;
        return this;
    }
    
    addObjectTag(object) {
        this.createObjectTag(object);
        return this;
    }
    
    addChild(tagName, style) {
        this.createChild(tagName, style);
        return this;
    }
    
    addTextChild(text, style) {
        this._jsonML.push(['span', {style: style || ''}, `${String(text)}`]);
        return this;
    }
    
    toJsonML() {
        return this._jsonML;
    }
}