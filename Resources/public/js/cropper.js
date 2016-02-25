

var Cropper = function (container) {
    console.log("Creating new");
    this.formats = container.data('formats');
    this.nFormats = 0;
    var crop_id_prefix = '#'+container.data('crop-id');
    this.id_prefix = '#'+container.data('id');
    console.log(this.id_prefix);
    this.cropForms = {};
    this.cropData = {};
    this.imgSrc = container.data('source');
    this.image = container.data('image');
    this.currentFormat = null;
    this.defaultFormat = null;
    this.color = $(this.id_prefix+"_bgColor").val();
    this.check_color_link = $('#bg-color').data('check-link');
    this.borderize = $(this.id_prefix+"_cropOutside").val() == 1;
    var ref = this;
    $.each(this.formats,
        function(name, v){
            if(ref.defaultFormat == null) {
                ref.defaultFormat = name;
            }
            this.nFormats++;
            ref.cropForms[name] = $(crop_id_prefix+"_"+name);
            ref.cropData[name] = JSON.parse(ref.cropForms[name].val());
        }
    );

    this.formatMenu = {};

    $('#crop-save').click(function(){ref.save()});
};
Cropper.jcrop_api = null;
Cropper.prototype.show = function(){
    this.loadMenu();
    this.changeToFormat(this.defaultFormat);
    this.loadBorderizer();
    $('#crop-modal').modal();


}
Cropper.prototype.loadBorderizer = function()
{
    var self = this;
    $('#bg-color').val(this.color);
    if(this.borderize === false){
        $('#bg-color').prop('disabled',true);
    }
    $('#bg-color').keyup(function(e){self.changeColor(e);});
    if(this.borderize) {
        $('#border-image').iCheck('check');
    }
    $('#border-image').on('ifToggled',function(e){self.changeBorderize(e);});

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
    var f = this.formats[this.currentFormat];
    var str = this.imgSrc+"&width="+f.width+"&height="+f.height+"&bg="+encodeURIComponent(this.color);
    if(this.borderize) {
        str += "&border=1";
    }
    if(Cropper.jcrop_api != null){
        Cropper.jcrop_api.destroy();
    }
    $('#img-holder').html('');
    var self = this;
    $('#waiting4image').show();
    $('#img-holder').html('<img src="" id="crop-source-image">');
    $("#crop-source-image").on('load',function(){
        $('#waiting4image').hide();
        self.reloadJcrop();
    });
    $("#crop-source-image").attr('src',str);

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
    options.bgColor = 'red';
    var ref = this;
    options.onSelect = function(c){ ref.changeCrop(c);};
    if(crop.type == 'custom')
    {
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

Cropper.prototype.changeColor = function(e)
{
    var t = $(e.currentTarget);
    this.color = t.val();
    this.checkColor();

}

Cropper.prototype.changeBorderize = function(c)
{
    var cb = $(c.currentTarget);
    this.borderize = cb.is(":checked");
    if(this.borderize) {
        $('#bg-color').prop('disabled',false);
        this.checkColor();
    } else {
        $('#bg-color').val('');
        $('#bg-color').prop('disabled',true);
        this.loadImage();
        this.color = '';
    }

}
Cropper.prototype.checkColor = function ()
{
    var self = this;
    if(this.xhr && this.xhr.readystate != 4){
        this.xhr.abort();
    }
    this.xhr =  $.get(
        this.check_color_link,
        {'image':this.image, 'color':this.color} ,
        function(r){
            r = JSON.parse(r);
            if(r.error){
                $('#color-group').addClass("has-error");
            } else {

                $('#color-group').removeClass("has-error");
                if(self.color == '') {
                    self.color = r.color;
                    $('#bg-color').val(self.color);
                }
                self.loadImage();
            }

        }
    );
}

Cropper.prototype.changeToFormat = function(format) {
    if(this.currentFormat != null) {
        this.formatMenu[this.currentFormat].removeClass("active");
    }
    this.currentFormat = format;
    this.formatMenu[format].addClass("active");
    this.loadImage();
 }

Cropper.prototype.save = function() {
    var ref = this;
    $.each(this.formats,
        function(name, v){
            ref.cropForms[name].val(JSON.stringify(ref.cropData[name]));

        }
    );

    this.borderize = $(this.id_prefix+"_cropOutside").val(this.borderize ? '1' : '0');
    $(this.id_prefix+"_bgColor").val(this.color);
    $('#crop-modal').modal('hide');
}