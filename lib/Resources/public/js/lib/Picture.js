ag.ns("ag.admin.field");

(function(){

var defaults =
    {
        maxWidth : 400,
        maxHeight : 400,
        type : "image/jpeg"
    },

    ImageField = function(options)
    {
        this.extend(this, ag.u.tpl("agit-images-admin-field", ".thumb"));

        this.options = Object.assign({}, defaults, options || {});
        this.image = new Image();
        this.origImage = new Image();

        // because of async stuff, we must store a consistent state separately
        this.currentValue = { description : "" };

        var tCanvas = this.find(".preview")[0],
            currentRotation = 0;

        // triggers when this.origImage.src is updated (setValue)
        this.origImage.addEventListener("load", () => {
            this.image.src = resizeCanvas(
                    imageToCanvas(this.origImage),
                    this.options.maxWidth,
                    this.options.maxHeight).toDataURL(this.options.type);
        });

        // triggers when this.image.src is updated (setValue, resizing, rotating)
        this.image.addEventListener("load", () => {
            updateThumbnail.call(this, this.image, tCanvas);
            this.currentValue.data = ImageField.urlToObj(this.image.src).data;
            this.removeClass("processing");
        });

        this.find(".enlarge").click(ev => {
            if (!this.is(".processing"))
            {
                showPreview(this.image);
            }
        });

        this.find(".desc").click(ev => {
            if (!this.is(".processing"))
            {
                var modal = new ag.image.DescriptionModal(this.currentValue.description, value => {
                    this.toggleClass("has-comment", !!value);
                    this.currentValue.description = value;
                });
                modal.appear();
            }
        });

        this.find(".rotate").click(ev => {
            if (!this.is(".processing"))
            {
                this.addClass("processing");
                currentRotation = ++currentRotation % 4;

                // delay processing a bit, because in some envs the browser may freeze and not show the indicator
                setTimeout(() => {
                    this.image.src = resizeCanvas(
                        rotateCanvas(
                            imageToCanvas(this.origImage),
                            currentRotation
                        ),
                        this.options.maxWidth,
                        this.options.maxHeight
                    ).toDataURL(this.options.type);
                }, 100);

            }
        });

        this.find(".remove").click(ev => {
            if (!this.is(".processing"))
            {
                this.trigger("ag.img.remove");
                this.remove();
            }
        });
    },

    imageToCanvas = function(image)
    {
        var vCanvas = document.createElement("canvas"),
            vCanvasCtx = vCanvas.getContext("2d");

        vCanvas.width = image.width;
        vCanvas.height = image.height;

        vCanvasCtx.drawImage(image, 0, 0);

        return vCanvas;
    },

    showPreview = function(image)
    {
        var elem = ag.u.tpl("agit-images-admin-field", ".large-image"),
            modal = new ag.ui.modal.Display();

        elem.find("img").attr("src", image.src);
        modal.addCloseButton();
        modal.setContent(elem);
        modal.appear();
    },

    updateThumbnail = function(image, tCanvas)
    {
        var tFactor = Math.max(image.width / tCanvas.width, image.height / tCanvas.height),
            tContext = tCanvas.getContext("2d");
            tWidth = image.width / tFactor,
            tHeight = image.height / tFactor;

        tContext.clearRect(0, 0, tCanvas.width, tCanvas.height);
        tContext.drawImage(image, (tCanvas.width - tWidth) / 2, (tCanvas.height - tHeight) / 2, tWidth, tHeight);
    },

    // NB: returns a modified clone of the canvas
    resizeCanvas = function(canvas, maxWidth, maxHeight)
    {
        var resizeFactor = Math.max(canvas.width / maxWidth, canvas.height / maxHeight),
            vCanvas = document.createElement("canvas"),
            vCanvasCtx = vCanvas.getContext("2d");

        vCanvas.width = canvas.width;
        vCanvas.height = canvas.height;

        vCanvasCtx.drawImage(canvas, 0, 0);

        if (resizeFactor > 1)
            hermiteResize(vCanvas, canvas.width / resizeFactor, canvas.height / resizeFactor);

        return vCanvas;
    },

    // NB: returns a modified clone of the canvas
    rotateCanvas = function(canvas, steps)
    {
        var vCanvas = document.createElement("canvas"),
            vCanvasCtx = vCanvas.getContext("2d");

        vCanvas.width = canvas.width;
        vCanvas.height = canvas.height;

        vCanvasCtx.save();

        if (steps % 2)
        {
            vCanvas.width = canvas.height;
            vCanvas.height = canvas.width;
        }

        vCanvasCtx.translate(vCanvas.width / 2, vCanvas.height / 2);

        if (steps)
        {
            vCanvasCtx.rotate(Math.PI / 2 * steps);
        }

        vCanvasCtx.drawImage(canvas, -(canvas.width / 2), -(canvas.height / 2));
        vCanvasCtx.restore();

        return vCanvas;
    };

ImageField.prototype = Object.create(jQuery.prototype);

ImageField.prototype.getValue = function()
{
    var value = new ag.api.Object("admin.v1/Image", this.currentValue);

    Object.keys(value).forEach(key => {
        if (value.getPropMeta(key).readonly)
            value[key] = null;
    });

    return value;
};

ImageField.prototype.setValue = function(value)
{
    this.addClass("processing").toggleClass("has-comment", !!value.description);
    this.currentValue = value;
    this.origImage.src = ImageField.objToUrl(value);
};

// used for insertions via file select dialog
ImageField.prototype.setValueFromBlobUrl = function(blobUrl)
{
    this.addClass("processing");
    this.currentValue = { description : "" };
    this.origImage.src = blobUrl;
    this.removeClass("has-comment");
};

// static
ImageField.objToUrl = function(imgObj, type)
{
    return "data:" +  (type || imgObj.type || defaults.type) + ";base64," + imgObj.data;
};

// static
ImageField.urlToObj = function(dataUrl)
{
    var rawParts = dataUrl.split("base64,");

    return new ag.api.Object("admin.v1/Image", {
        type : rawParts[0].replace(/^data:/, ""),
        data : rawParts[1]
    });
};

ag.admin.field.Image = ImageField;

})();
