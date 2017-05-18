/**
 * Created by Sam Washington on 5/6/17.
 */
import {_entries} from "../util/index";
import JsonMLElement from "./JsonMLElement";

export const DEBUG = Symbol('DEBUG');

class SimpleFormatter {
    matches(item) {
        return true;
    }
    
    description(object) {
        return (typeof object === "object") && object ? object.constructor.name : object;
    }
    
    hasChildren(object) {
        return (typeof object === "object");
    }
    
    children(object) {
        const result = [];
        if (object) {
            for (let [key, val] of _entries(object)) {
                if (typeof key === "symbol") key = key.toString();
                if (typeof val === "symbol") val = val.toString();
                result.push({key: key, value: val});
            }
        }
        
        return result;
    }
}
let _l = [];
class _DebugFormatter extends SimpleFormatter {
    matches(item) {
        _l.push(item);
        return typeof item === 'object' && item && this.convert(item).name;
    }
    
    /**
     *
     * @param item
     * @return {{name: string|null, values: (*[]|*)}}
     * @private
     */
    convert(item) {
        let obj = item[DEBUG];
        if (!obj || !('name' in obj)) return {name: null, values: null};
        obj.values = obj.values || [];
        return item[DEBUG];
    }
    
    description(item) {
        item     = this.convert(item);
        let name = item.name;
        
        if (item.template_type) {
            let _json_ml = (txt, color = '#546050', element) => {
                element = element || JsonMLElement.init('span');
                
                if (typeof txt === 'string')
                    return element.addTextChild(String(txt), `color: ${color}`);
                else
                    return element.addObjectTag(txt, `color: ${color}`);
            };
            
            let element = _json_ml(name, '#828789');
            _json_ml('<', '#c0c6a9', element);
            _json_ml({item: item.template_type, [DEBUG](){return {name: item.template_type}}}, '#747a61', element);
            _json_ml('>', '#c0c6a9', element);
            return element;
        }
        return name + ' !! ';
    }
    
    hasChildren(item) {
        let values = this.convert(item).values;
        return !!values && ((Array.isArray(values) && values.length) || typeof values === "object");
    }
    
    children(item) {
        return super.children(this.convert(item).values || [])
    }
}

class Formatter {
    static init() { return new this(...arguments); }
    
    constructor(simpleFormatter) {
        this._simpleFormatter = simpleFormatter || new SimpleFormatter;
    }
    
    header(object) {
        if (object instanceof Node) return null;
        if (!this._simpleFormatter.matches(object)) return null;
        
        let description = this._simpleFormatter.description(object);
        let element     =
                (description instanceof JsonMLElement)
                    ? description
                    : JsonMLElement.init('span').addTextChild(String(description));
        
        return element.toJsonML();
    }
    
    hasBody(object) {
        return this._simpleFormatter.hasChildren(object);
    }
    
    body(object) {
        const body     = new JsonMLElement("ol");
        const children = this._simpleFormatter.children(object);
        body.setStyle("list-style-type:none; padding-left: 0px; margin-top: 0px; margin-bottom: 0px; margin-left: 12px");
        for (const child of children) {
            const li      = body.createChild("li");
            let objectTag =
                      typeof child.value === "object"
                          ? li.createObjectTag(child.value)
                          : li.createChild("span");
            
            const nameSpan = objectTag.createChild("span");
            nameSpan.addTextChild(String(child.key) + ": ");
            
            if (child.value instanceof Node) {
                const node = child.value;
                objectTag.addTextChild(node.nodeName.toLowerCase());
                objectTag.addTextChild(node.id ? `#${node.id}` : `.${node.className}`)
            } else if (typeof child.value !== "object") {
                objectTag.addTextChild("" + String(child.value));
            }
        }
        return body.toJsonML();
    }
    
    _arrayFormatter(array) {
        const j = new JsonMLElement();
        j.addTextChild("[");
        for (let i = 0; i < array.length; ++i) {
            if (i !== 0)
                j.addTextChild(", ");
            j.createObjectTag(array[i]);
        }
        j.addTextChild("]");
        return j;
    }
}

//window["devtoolsFormatters"] = [Formatter.init(new _DebugFormatter), Formatter.init()];