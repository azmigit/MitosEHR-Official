/**
 * @class Ext.grid.TableChunker
 * 
 * Produces optimized XTemplates for chunks of tables to be
 * used in grids, trees and other table based widgets.
 *
 * @singleton
 */
Ext.define('Ext.grid.TableChunker', {
    singleton: true,
    requires: ['Ext.XTemplate'],
    metaTableTpl: [
        '{[this.openTableWrap()]}',
        '<table class="' + Ext.baseCSSPrefix + 'grid-table ' + Ext.baseCSSPrefix + 'grid-table-resizer" border="0" cellspacing="0" cellpadding="0" {[this.embedFullWidth()]}>',
            '<tbody>',
            '<tr>',
            '<tpl for="columns">',
                '<th class="' + Ext.baseCSSPrefix + 'grid-col-resizer-{id}" style="width: {width}px; height: 0px;"></th>',
            '</tpl>',
            '</tr>',
            '{[this.openRows()]}',
                '{row}',
                '<tpl for="features">',
                    '{[this.embedFeature(values, parent, xindex, xcount)]}',
                '</tpl>',
            '{[this.closeRows()]}',
            '</tbody>',
        '</table>',
        '{[this.closeTableWrap()]}'
    ],

    constructor: function() {
        Ext.XTemplate.prototype.recurse = function(values, reference) {
            return this.apply(reference ? values[reference] : values);
        };
    },

    embedFeature: function(values, parent, x, xcount) {
        var tpl = '';
        if (!values.disabled) {
            tpl = values.getFeatureTpl(values, parent, x, xcount);
        }
        return tpl;
    },

    embedFullWidth: function() {
        return 'style="width: {fullWidth}px;"';
    },

    openRows: function() {
        return '<tpl for="rows">';
    },

    closeRows: function() {
        return '</tpl>';
    },

    metaRowTpl: [
        '<tr class="' + Ext.baseCSSPrefix + 'grid-row {[this.embedRowCls()]}">',
            '<tpl for="columns">',
                '<td class="' + Ext.baseCSSPrefix + 'grid-cell ' + Ext.baseCSSPrefix + 'grid-cell-{id} {{id}-modified} {{id}-tdCls}" {{id}-tdAttr}><div unselectable="on" class="' + Ext.baseCSSPrefix + 'grid-cell-inner ' + Ext.baseCSSPrefix + 'unselectable" style="{{id}-style}; text-align: {align};">{{id}}</div></td>',
            '</tpl>',
        '</tr>'
    ],
    embedRowCls: function() {
        return '{rowCls}';
    },
    openTableWrap: function() {
        return '';
    },
    closeTableWrap: function() {
        return '';
    },

    getTableTpl: function(cfg, textOnly) {
        var tpl,
            tableTplMemberFns = {
                openRows: this.openRows,
                closeRows: this.closeRows,
                embedFeature: this.embedFeature,
                embedFullWidth: this.embedFullWidth,
                openTableWrap: this.openTableWrap,
                closeTableWrap: this.closeTableWrap
            },
            tplMemberFns = {},
            features = cfg.features || [],
            ln = features.length,
            i  = 0,
            memberFns = {
                embedRowCls: this.embedRowCls
            },
            // copy the default
            metaRowTpl = Array.prototype.slice.call(this.metaRowTpl, 0),
            metaTableTpl;
            
        for (; i < ln; i++) {
            if (!features[i].disabled) {
                features[i].mutateMetaRowTpl(metaRowTpl);
                Ext.apply(memberFns, features[i].getMetaRowTplFragments());
                Ext.apply(tplMemberFns, features[i].getTplFragments());
                Ext.apply(tableTplMemberFns, features[i].getTableFragments());
            }
        }
        
        metaRowTpl = new Ext.XTemplate(metaRowTpl.join(''), memberFns);
        cfg.row = metaRowTpl.applyTemplate(cfg);
        
        metaTableTpl = new Ext.XTemplate(this.metaTableTpl.join(''), tableTplMemberFns);
        
        tpl = metaTableTpl.applyTemplate(cfg);
        
        // TODO: Investigate eliminating.
        if (!textOnly) {
            tpl = new Ext.XTemplate(tpl, tplMemberFns);
        }
        return tpl;
        
    }
});
