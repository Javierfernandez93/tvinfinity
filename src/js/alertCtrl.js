var alertCtrl = {
    modal : false,
    body : false,
    footer : false,
    createUUID : function()
    {
        var d = new Date().getTime();

        if (window.performance && typeof window.performance.now === "function") {
            d += performance.now(); 
        }

        var uuid = 'Zxxx-Axxx'.replace(/[xy]/g, function (c) {
            var r = (d + Math.random() * 16) % 16 | 0;
            d = Math.floor(d / 16);
            return (c == 'x' ? r : (r & 0x3 | 0x8)).toString(16);
        });

        return uuid;
    },
    showMessage : function(title,subTitle)  {
        this.modal({
            title : title,
            subTitle : subTitle,
        });
    },
    create : function(options)  {
        var defaults = {
            title: "", 
            subTitle: false,
            imgTop: false,
            html: false,
            bgClass: "",
            closeButton: true,
            id: this.createUUID(),
            open: function () { }, 
            buttons: [],
            inputs: [],
            size: 'modal-sm',
            bgColor: 'bg-light',
        };

        this.settings = $.extend(true, {}, defaults, options);

        var $modal = $("<div />").attr("id", this.settings.id).attr("role", "dialog").addClass(`modal ${this.settings.bgClass} fade`).attr("tabindex","-1").attr("aria-lalledby",this.settings.id).attr("aria-modal","true");
        
        var $dialog = $("<div />").addClass(`modal-dialog modal-dialog-centered `+this.settings.size);
        var $content = $("<div />").addClass(`modal-content ${this.settings.bgColor}`);

        if(this.settings.imgTop)
        {
            var $imgTop = $("<img />").attr("src",this.settings.imgTop).addClass("card-img-top").attr("alt","model");

            $content.append($imgTop);
        }

        $dialog.append($content);

        var $header = $("<div />").addClass("modal-header border-0");
        
        if(this.settings.imgTop)
        {
            $header.addClass("floating d-flex justify-content-between w-100")
        }
        
        var $rowHeader = $("<div />").addClass("d-flex justify-content-between w-100");

        if(this.settings.title)
        {
            var $col = $("<div />").addClass("");
            
            $col.append($("<h4 />").addClass("modal-title").text(this.settings.title));

            $rowHeader.append($col);
        }

        if(this.settings.closeButton)
        {
            var $colAuto = $("<div />").addClass("");

            $colAuto.append($("<button />").attr("type", "button").addClass("close btn btn-sm px-3 shadow-none btn-outline-dark waves-effect waves-light").html('<i class="fas fa-times"></i>').click(function () { $modal.dismiss() }));
            
            $rowHeader.append($colAuto);
        }

        $header.append($rowHeader)

        $content.append($header);

        if(this.settings.subTitle)
        {
            $content.append($("<div />").addClass("modal-body").html(this.settings.subTitle));
        }

        if(this.settings.html)
        {
            $content.append($("<div />").addClass("modal-body").html(this.settings.html));
        }

        $content.append($("<div />").addClass("modal-footer border-0"));

        if(this.settings.cssClass)
        {
            $content.find('.modal-footer').addClass(this.settings.cssClass);
        }

        $modal.append($dialog);
        
        $modal.shown = false;

        $modal.dismiss = function(callback)
        {
            $modal.modal('hide');

            this.settings = $.extend(true, {}, defaults, options);
            object_id = this.settings.id;

            window.setTimeout(()=>{
                $("body").removeClass("modal-open");
                $(".responsive-bootstrap-toolkit").remove();
                $(".modal-backdrop").remove();
                $("#"+object_id).remove();

                if(callback != undefined) { callback();}
            }, 500);
        }

        // add the buttons
        var $footer = $modal.find(".modal-footer");
        var $body = $modal.find(".modal-body");
        var hasInput = this.settings.inputs.length ? true : false;

        this.modal = $modal;
        this.body = $body;
        this.footer = $footer;

        for(var i=0; i < this.settings.inputs.length; i++) {
            this.addInput(this.settings.inputs[i]);
        }

        for(var i=0; i < this.settings.buttons.length; i++)  {
            if(i == 0 && this.settings.buttons[i].class == undefined) {
                this.settings.buttons[i].class = "btn-primary rounded-pill px-3 waves-light waves-effect";
            }

            this.addbutton(this.settings.buttons[i]);
        }

        this.settings.open($modal);

        $modal.on('shown.bs.modal', function (e) {
            $modal.shown = true;
        });

        return this;
    },
    modal : function(settings)  {
        let alert = this.create(settings);
        this.present(alert.modal);
    },
    addInput : function(input)  {
        if(input)
        {
            let id = input.id ? input.id : this.createUUID();
            input.id = id;

            if(input.type === "radio")
            {
                let div = $("<div />").addClass("custom-control custom-radio pb-2 mb-2 border-bottom");
                let _input = $("<input />").addClass("form-control underlined")
                    .attr("class", "custom-control-input")
                    .attr("id", id)
                    .attr("type", input.type)
                    .attr("name", input.name)
                    .attr("placeholder", input.placeholder)
                    .val(input.value);
                
                let label = $("<label />").addClass("form-control underlined")
                    .attr("class", "custom-control-label")
                    .attr("for", id)
                    .text(input.text);

                    div.append(_input,label);

                this.body.append(div);
            } else {
                let div = $("<div />").addClass("form-group");

                if(input.label != undefined)
                { 
                    div.append($("<label />").attr("for", id).text(input.label));    
                }

                let _input = $("<input />").addClass("form-control")
                    .attr("id", id)
                    .attr("type", input.type)
                    .attr("name", input.name)
                    .val(input.value)
                    .attr("placeholder", input.placeholder);

                if(input.min != undefined)
                {
                    _input.attr("min",input.min);
                }

                if(input.maxLenght != undefined)
                {
                    _input.attr("maxlength",input.maxLenght);
                }

                div.append(_input);
                this.body.append(div);
            }
        }
    },
    addbutton : function(btn)  {
        let id = btn.id ? btn.id : this.createUUID();
            btn.id = id;

        let _class = btn.class == undefined ? "btn-light" : btn.class;
        
        this.footer.prepend($("<button />").addClass("btn shadow-none mb-0 "+_class)
            .attr("id", id)
            .attr("type", "button")
            .text(btn.text)
            .click(()=>
            {
                var hasInput = this.settings.inputs.length ? true : false;
                if(hasInput)
                {
                    btn.handler(this.getInputsData(this.settings.inputs));
                    this.modal.dismiss();
                } else {
                    btn.handler(this.modal)
                }

                if(btn.role == 'cancel') 
                {
                    this.modal.dismiss();   
                }
            }))

    },
    present : function($modal)  {
        $modal.modal("show");
        
        setTimeout(()=>{
            $modal.find("button").eq(0).focus();
        },400);
    },
    getInputsData : function(inputs)  {
        var data = {};
        inputs.forEach((input)=>{
            if(input.type == "radio")
            {
                data[input.name] = $("input[name='"+input.name+"']").val();
            } else {
                data[input.name] = $("#"+input.id).val();
            }
        });
        return data;
    },
};