

var Cropper = function (container) {
    console.log("Creating new");
    this.formats = container.data('formats');
    this.nFormats = 0;
    var id_prefix = '#'+container.data('crop-id')
    this.cropForms = {};
    this.cropData = {};
    this.imgSrc = container.data('source');
    this.currentFormat = null;
    this.defaultFormat = null;
    var ref = this;
    $.each(this.formats,
        function(name, v){
            if(ref.defaultFormat == null) {
                ref.defaultFormat = name;
            }
            this.nFormats++;
            ref.cropForms[name] = $(id_prefix+"_"+name);
            ref.cropData[name] = JSON.parse(ref.cropForms[name].val());
        }
    );

    this.formatMenu = {};

    $('#crop-save').click(function(){ref.save()});
};
Cropper.jcrop_api = null;
Cropper.prototype.show = function(){
    this.loadMenu();
    this.loadImage();
    this.changeToFormat(this.defaultFormat);
    $('#crop-modal').modal();


}

Cropper.prototype.loadMenu = function()
{
    var menu = $('<ul class="nav nav-pills nav-stacked"></ul>');
    var ref = this;
    $.each(this.formats,
        function(name, v){
            ref.formatMenu[name] = $("<li><a href=\"\">"+name+"</a></li>")
            menu.append(ref.formatMenu[name]);
        }
    );
    if(this.nFormats > 1 && !$('#crop-format-bar').hasClass("col-sm-3")){
        $('#crop-format-bar').addClass("col-sm-3");
        $('#crop-format-bar').removeClass("col-sm-0");
        $('#crop-format-photo').removeClass("col-sm-12");
        $('#crop-format-photo').addClass("col-sm-9");
    } else if(this.nFormats  < 2 && $('#crop-format-bar').hasClass("col-sm-3")) {
        $('#crop-format-bar').removeClass("col-sm-3");
        $('#crop-format-bar').addClass("col-sm-0");
        $('#crop-format-photo').addClass("col-sm-12");
        $('#crop-format-photo').removeClass("col-sm-9");
    }
    if(this.nFormats  < 2 )
    {
        $('#crop-format-bar').html("");
    }
    else {
        $('#crop-format-bar').html(menu);
    }

}

Cropper.prototype.loadImage = function ()
{
    console.log(this.imgSrc);
    $("#crop-source-image").attr('src',this.imgSrc);

}
Cropper.prototype.reloadJcrop = function()
{
    if(Cropper.jcrop_api != null){
        Cropper.jcrop_api.destroy();
    }
    var width = 800;
    if(this.formats.length > 1) {
        width = 500;
    }
    var options = {boxWidth:width, boxHeight: 500};
    var format = this.formats[this.currentFormat];
    var crop = this.cropData[this.currentFormat];
    options.minSize = [format.width, format.height];
    options.aspectRatio = format.width/format.height;
    var ref = this;
    options.onSelect = function(c){ ref.changeCrop(c);};
    if(crop.type == 'custom')
    {
        console.log("Go gogo");
        options.setSelect = [crop.customCrop.startx,crop.customCrop.starty,crop.customCrop.startx + crop.customCrop.width, crop.customCrop.starty + crop.customCrop.height];
    }
     $("#crop-source-image").Jcrop(options, function(){Cropper.jcrop_api = this;});
}

Cropper.prototype.changeCrop = function(c)
{
    console.log(this.cropData[this.currentFormat]);
    this.cropData[this.currentFormat].type = 'custom';
    this.cropData[this.currentFormat].customCrop = {"startx": c.x, "starty": c.y, "width": c.w, "height": c.h};
}

Cropper.prototype.changeToFormat = function(format) {
    if(this.currentFormat != null) {
        this.formatMenu[this.currentFormat].removeClass("active");
    }
    this.currentFormat = format;
    this.formatMenu[format].addClass("active");
    this.reloadJcrop();
 }

Cropper.prototype.save = function() {
    console.log("Closing");
    var ref = this;
    $.each(this.formats,
        function(name, v){
            ref.cropForms[name].val(JSON.stringify(ref.cropData[name]));

        }
    );
    $('#crop-modal').modal('hide');
}