/**
 * Created by Dale on 2015-05-20.
 */
var globalTableData = {};

var modalDialog =
    '<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">' +
    '<div class="modal-dialog modal-sm">' +
    '<div class="modal-content">' +
    '<div class="modal-body">' +
    '<p></p>' +
    '</div>' +
    '<div class="modal-footer">' +
    '<button type="button" class="btn btn-primary">Confirm</button>' +
    '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>' +
    '</div>' +
    '</div>' +
    '</div>' +
    '</div>';

function LoadTableData( data )
{
    globalTableData = data;
}

function Table( props, enums, actions )
{
    this.rowProperties = props;
    this.rowActions = actions;
    this.typeEnums = enums;
    this.data = [];
}

Table.prototype.getRowObj = function( rowID )
{
    return globalTableData[rowID];
}

Table.prototype.build = function( parent ) {
    this.parent = $(parent);
    this.table = $("<table>").appendTo( parent ).addClass("players");
    var header = $("<tr>").appendTo( this.table );

    $("<th>").appendTo(header);
    this.eachRowProperty( function( name, display ) {
        $("<th>").appendTo(header).text(display);
    } );

    this.buildMenuSystem();
}

Table.prototype.take = function( parent ) {
    this.parent = $(parent);
    this.table = $( "table", parent );

    this.data = $( "input[name='id']", this.table ).map( function() {
        return parseInt( $(this).val() );
    }).get().sort(function(a,b) { return a - b; });

    this.buildMenuSystem();

    var thisTable = this;

    if( !$.isEmptyObject( this.rowActions ) )
    {
        $("tr.player", this.table).on("contextmenu", function(event) {
            thisTable.onContext( this, event );
            return false;
        });
    }
}

Table.prototype.onChange = function( input )
{
    if( $(input).is(":checked") ) $(input).parents("tr.player").first().addClass("over");
    else $(input).parents("tr.player").first().removeClass("over");
}

Table.prototype.onContext = function( row, event )
{
    var thisTable = this;
    if( this.boxes )
    {
        $("input[name='id']", row).prop("checked", true);
        this.selectedId = [];
        $("input[name='id']:checked", this.table).each( function() {
            thisTable.selectedId.push( $(this).val() );
        });
    }
    else
    {
        this.selectedId = parseInt($("input[name='id']", row).val());
    }

    this.amenu.show().position({
        my: "left top",
        of: event
    });
    $( document ).one( "click", function() {
        thisTable.amenu.hide();
        $("tr.over", thisTable.table).removeClass("over");
    });
    $(row).addClass("over");
}

Table.prototype.buildMenuSystem = function() {
    // don't bother if there aren't any actions (it's faster this way)
    if( $.isEmptyObject( this.rowActions ) ) return;

    this.amenu = $("<ul>").appendTo(this.parent).addClass("context-menu");
    this.aform = $("<form method='post' action=''>").appendTo(this.parent);
    $("<input type='hidden' name='id' value='' />").addClass("hiddenid").appendTo(this.aform);
    $("<input type='hidden' name='action' value=''/>").addClass('hiddensubmit').appendTo(this.aform);
    var thisTable = this;
    this.eachActionType( function( name, type, func, conf, message, columns ) {
        var li = $("<li>").appendTo(thisTable.amenu);
        var a = $("<a href='#'>").appendTo(li).html(name);

        if( type == "js" )
        {
            a.click( function() {
                if( thisTable.boxes )
                {
                    $("input[name='id']", thisTable.table).prop("checked", false);
                }
                func(thisTable.selectedId);
            });
        }
        else if( type == "post" )
        {
            a.click( function() {
                $(".hiddenid", thisTable.aform).val( thisTable.selectedId );
                $(".hiddensubmit", thisTable.aform).val( name );

                if( !conf ) {
                    thisTable.aform.submit();
                }
                else {
                    var pl = "";
                    if( $.isArray( thisTable.selectedId ) )
                    {
                        $(thisTable.selectedId).each( function() {
                            var table = this;
                            $(columns).each( function () {
                                pl += table[this] + " ";
                            });
                            pl += ", ";
                        });
                        pl = pl.substring(0, pl.length - 2);
                    }
                    else
                    {
                        var obj = thisTable.getRowObj( thisTable.selectedId );
                        //pl = obj["title"];//obj["first_name"] + " " + obj["last_name"];
                        $(columns).each( function () {
                            pl += obj[this] + " ";
                        });
                        pl = pl.substring(0, pl.length - 1);
                    }
                    thisTable.getRowObj( thisTable.selectedId )
                    $("p", thisTable.aconfirm).text(message + " " + pl + "?");
                    thisTable.aconfirm.modal("show");
                }
                return false;
            });
        }
    });

    this.aconfirm = $(modalDialog).appendTo(thisTable.parent);
    $('button', this.aconfirm).first().click(function( ) {
        thisTable.aform.submit();
    });


    // Hide all UI elements
    this.amenu.menu().hide();
    this.aform.hide();

    // Select All/None buttons
    if( this.boxes )
    {
        var p = $("<p>").prependTo( this.parent ).append( $("<button>").html("Select All").click( function() {
            // Select All
            $("input[name='id']", thisTable.table).prop("checked", true).parents("tr.player").addClass("over");
        } ) ).append( $("<button>").html("Select None").click( function() {
            // Select None
            $("input[name='id']", thisTable.table).prop("checked", false).parents("tr.player").removeClass("over");
        } ) );
    }
}

Table.prototype.eachRowProperty = function(callback) {
    $.each( this.rowProperties, function(name, data) { callback(name, data.display, data.type); });
}

Table.prototype.eachActionType = function(callback) {
    $.each( this.rowActions, function(name, data) {
        var confirm = false;
        if( "confirm" in data )
        {
            confirm = data.confirm;
        }
        callback(name, data.type, data.func, confirm, data.message, data.columns);
    });
}

Table.prototype.setRowData = function( entry, data ) {
    var thisTable = this;
    this.eachRowProperty( function(name, display, type) {
        var row = $("td." + name, entry).text(thisTable.translateToString( data[name], type ));
    });
}

Table.prototype.addRow = function( rowID, bulk ) {
    if( bulk == null || bulk == undefined ) bulk = false;
    if( this.getRow(rowID).size() != 0 ) return false;

    var entry = $("<tr>").appendTo(this.table).addClass("player");
    var thisTable = this;

    var idRow = $("<td style='display:none;'>").appendTo(entry);
    if( this.boxes )
    {
        idRow.append($("<input name='id' type='checkbox'>").val(rowID));
        $("input", idRow).change( function() {
            thisTable.onChange(this);
        });
    }
    else
    {
        idRow.append($("<input name='id' type='hidden'>").val(rowID));
    }

    this.eachRowProperty( function( name, display, type ) {
        var row = $("<td>").appendTo(entry).addClass(name);
    });

    this.setRowData( entry, globalTableData[rowID] );
    this.data.push( rowID );

    if( !$.isEmptyObject( this.rowActions ) )
    {
        entry.on("contextmenu", function(event) {
            thisTable.onContext( this, event );
            return false;
        });
    }
    if( !bulk ) this.data.sort(function(a,b) { return a - b; });
    return true;
}

Table.prototype.addData = function( data ) {
    for( var i = 0; i < data.length; i++ ) {
        this.addRow( data[i], true );
    }

    // bulk mode disables sorting per-insert, so sort after the bulk is finished
    this.data.sort(function(a,b) { return a - b; });
}

Table.prototype.geRowIDs = function() {
    return this.data.slice(0);
}

Table.prototype.hasRow = function( rowID ) {
    for( var i = 0; i < this.data.length; i++ ) {
        if( this.data[i] == rowID ) return true;
        if( this.data[i] < rowID ) return false;
    }
    return false;
}

Table.prototype.removeRow = function( rowID ) {
    var row = this.getRow(rowID);
    if( row.size() == 0 ) return false;
    row.remove();

    // remove from array
    var index = $.inArray( rowID, this.data );
    if( index != -1 )
    {
        this.data.splice( index, 1 );
    }
    return true;
}
Table.prototype.clear = function () {
    $("tr.player", this.table).remove();
    this.data = [];
};
Table.prototype.count = function() {
    return this.data.length;
};
Table.prototype.getRow = function( rowID ) {
    return $("input[name=id][value=" + rowID + "]", this.table).parents("tr.player").first();
}

Table.prototype.translateToString = function( data, type ) {
    if( type == "text" ) {
        if( data == null ) {
            return "-NULL-";
        }
        else return data;
    }
    else if( type == "bool" ) {
        if( data == 0 ) return "False";
        else return "True";
    } else if( type in this.typeEnums ) {
        return this.typeEnums[type][data];
    }
    else { return data; }
}

