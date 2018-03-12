ag.ns("ag.admin.field");

(function(){

var defaults = { maxCount : 10 },

    imagesField = function(options)
    {
        this.extend(this, ag.u.tpl("agit-images-admin-field", ".images-field"));

        this.options = Object.assign({}, defaults, options || {});
        this.images = [];

        this.input = this.find("input");
        this.thumbs = this.find(".thumbs");
        this.add = this.find(".add").click(() => this.input.trigger("click"));

        this.input.change(() => {
            var files = [...this.input[0].files];
            this._addImages(files);
        });
    };

imagesField.prototype = Object.create(ag.ui.field.ComplexField.prototype);

imagesField.prototype.setValue = function(value)
{
    this.images = [];
    this.thumbs.empty();
    this._addImages(value);

    this.triggerHandler("ag.field.set");
    return this;
};

imagesField.prototype.getValue = function()
{
    return this.images.map(image => image.getValue());
};

imagesField.prototype._addImages = function(values)
{
    if (values && values.length && this.images.length < this.options.maxCount)
    {
        values.some(value => {
            var image = new ag.admin.field.Image(this.options);

            if (value instanceof File)
                image.setValueFromBlobUrl(URL.createObjectURL(value));
            else
                image.setValue(value);

            this.thumbs.append(image);
            this.images.push(image);

            image.on("ag.img.remove", () => {
                this.images.splice(this.images.indexOf(image), 1);

                if (this.images.length < this.options.maxCount)
                    this.add.show();
            });

            return this.images.length >= this.options.maxCount;
        });
    }

    if (this.images.length >= this.options.maxCount)
    {
        this.add.hide();
    }
};

ag.admin.field.Images = imagesField;

})();
