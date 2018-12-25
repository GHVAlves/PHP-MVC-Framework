'use strict';

/**
 * App loading
 */

var AppLoad = function () {

    /**
     * Starting and setting DataTables
     */

    $('.datatables').DataTable({
        "language": {
            "sEmptyTable": "Nenhum registro encontrado",
            "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
            "sInfoFiltered": "(Filtrados de _MAX_ registros)",
            "sInfoPostFix": "",
            "sInfoThousands": ".",
            "sLengthMenu": "Mostrar _MENU_ resultados por página",
            "sLoadingRecords": "Carregando...",
            "sProcessing": "Processando...",
            "sZeroRecords": "Nenhum registro encontrado",
            "sSearch": "Pesquisar",
            "oPaginate": {
                "sNext": "Próximo",
                "sPrevious": "Anterior",
                "sFirst": "Primeiro",
                "sLast": "Último"
            },
            "oAria": {
                "sSortAscending": ": Ordenar colunas de forma ascendente",
                "sSortDescending": ": Ordenar colunas de forma descendente"
            }
        }
    });

    /**
     * Creating masks with Jquery Mask
     */

    $('[mask="date"]').mask('00/00/0000');
    $('[mask="time"]').mask('00:00:00');
    $('[mask="datetime"]').mask('00/00/0000 00:00:00');
    $('[mask="cep"]').mask('00000-000');
    $('[mask="phone"]').mask('(00) 0000-0000');
    $('[mask="cellphone"]').mask('(00) 00000-0000');
    $('[mask="cpf"]').mask('000.000.000-00');
    $('[mask="cnpj"]').mask('00.000.000/0000-00');
    $('[mask="money"]').mask('R$ 000.000.000.000.000,00');
    $('[mask="decimal"]').mask('#.##0,00');
    $('[mask="percent"]').mask('##0,00%');

    /**
     * Starting and setting Jquery Validate
     */

    jQuery.extend(jQuery.validator.messages, {
        required: "Este campo é obrigatório.",
        remote: "Por favor, corrija este campo.",
        email: "Por favor, forneça um endereço eletrônico válido.",
        url: "Por favor, forneça uma URL válida.",
        date: "Por favor, forneça uma data válida.",
        dateISO: "Por favor, forneça uma data válida (ISO).",
        number: "Por favor, forneça um número válido.",
        digits: "Por favor, forneça somente dígitos.",
        creditcard: "Por favor, forneça um cartão de crédito válido.",
        equalTo: "Por favor, forneça o mesmo valor novamente.",
        accept: "Por favor, forneça um valor com uma extensão válida.",
        maxlength: jQuery.validator.format("Por favor, não forneça mais que {0} caracteres."),
        minlength: jQuery.validator.format("Por favor, forneça ao menos {0} caracteres."),
        rangelength: jQuery.validator.format("Por favor, forneça um valor entre {0} e {1} caracteres de comprimento."),
        range: jQuery.validator.format("Por favor, forneça um valor entre {0} e {1}."),
        max: jQuery.validator.format("Por favor, forneça um valor menor ou igual a {0}."),
        min: jQuery.validator.format("Por favor, forneça um valor maior ou igual a {0}.")
    });

    $("form").validate();

    /**
     * Load citys based on selected state
     */

    (function () {

        var createOption = function (value, text) {

            var option = document.createElement('option');
            option.value = value;
            option.text = text;

            return option;

        };

        var inputs = document.querySelectorAll('[state]');

        inputs.forEach(function (input) {

            input.addEventListener('change', function (event) {

                var target = event.target;
                var idState = target.options[target.selectedIndex].value;

                var request = new Request('/admin/user/getCitys/IdState/' + idState, {
                    headers: new Headers({
                        'Content-Type': 'application/json'
                    }),
                    method: 'GET'
                });

                fetch(request).then(function (response) {

                    if (response.status === 200) {

                        return response.json();

                    }
                    else {

                        throw new Error('Ooops! Internal server error, contact system administrator.');

                    }

                })
                .then(function (result) {

                    var selectCity = document.getElementById(target.getAttribute('data-target'));

                    if (result.length > 0) {

                        selectCity.innerHTML = '';
                        selectCity.appendChild(createOption('', 'Selecione'));

                        result.forEach(function (item) {

                            selectCity.appendChild(createOption(item.IdCity, item.Name));

                        });

                    }
                    else {

                        selectCity.innerHTML = '';
                        selectCity.appendChild(createOption('', 'Selecione o Estado'));

                    }

                });

            });

        });

    })();

    /**
     * Button delete of tables
     */

    (function () {

        var buttons = document.querySelectorAll('[data-delete]');

        buttons.forEach(function (button) {

            button.addEventListener('click', function (event) {
                
                event.preventDefault();

                app.confirmDialog({
                    title: 'Você tem certeza?',
                    html: 'Você deseja realmente remover este item? <br /> Esta operação não poderá ser revertida!',
                    type: 'warning'
                })
                .on('ok', function () {

                    var target = event.target;
                    var redirect = target.getAttribute('data-delete');

                    if (!redirect) {
                        
                        target = target.parentNode;
                        redirect = target.getAttribute('data-delete');

                    }

                    window.location = redirect;

                });

            });

        });

    })();

    /**
     * Starting and setting Jquery FroalaEditor
     */

    $.FroalaEditor.DefineIcon('insertVideo', { NAME: 'video' });
    $.FroalaEditor.DefineIcon('insertFile', { NAME: 'upload' });
    $.FroalaEditor.DefineIcon('emoticons', { NAME: 'grin' });
    $.FroalaEditor.DefineIcon('imageReplace', { NAME: 'exchange-alt' });
    $.FroalaEditor.DefineIcon('imageCaption', { NAME: 'font' });
    $.FroalaEditor.DefineIcon('videoReplace', { NAME: 'exchange-alt' });
    $.FroalaEditor.DefineIcon('linkOpen', { NAME: 'external-link-alt' });

    $('textarea[editor]').froalaEditor({
        language: 'pt_br',
        height: 300
    });

    /**
     * Select Menu and Page
     */

    (function () {

        var menu = document.getElementById('IdMenuParent');
        var page = document.getElementById('IdPage');
        var url = document.getElementById('URL');
        var level = document.getElementById('Level');

        if (menu && page && url && level) {

            var menuSelect = function(event) {

                var selected = menu.options[menu.selectedIndex];

                var request = new Request('/admin/menu/getNextMenuLevel/idMenu/' + ((selected.value) ? selected.value : 0), {
                    headers: new Headers({
                        'Content-Type': 'application/json'
                    }),
                    method: 'GET'
                });

                fetch(request).then(function (response) {

                    if (response.status === 200) {

                        return response.json();

                    }
                    else {

                        throw new Error('Ooops! Internal server error, contact system administrator.');

                    }

                })
                .then(function (result) {

                    if (event && event.type === 'change') {

                        level.value = result[0].Level;

                    }

                });

            };

            var pageSelect = function(event) {

                var selected = page.options[page.selectedIndex];
                
                if (selected.value) {
                    
                    url.value = '/page/' + selected.value;
                    url.setAttribute('readonly', 'readonly');

                }
                else {

                    if (event && event.type === 'change') {
                        
                        url.value = '';
                        url.removeAttribute('readonly');

                    }

                }

            };

            menu.addEventListener('change', menuSelect);
            menuSelect();

            page.addEventListener('change', pageSelect);
            pageSelect();
            
        }

    })();

    /**
     * Starting and setting Dropzone
     */

    (function () {

        /*
        Dropzone.prototype.defaultOptions.dictDefaultMessage = "Lore ipsum";
        Dropzone.prototype.defaultOptions.dictFallbackMessage = "Lore ipsum";
        Dropzone.prototype.defaultOptions.dictInvalidFileType = "Lore ipsum";
        Dropzone.prototype.defaultOptions.dictFileTooBig = "Lore ipsum ({{filesize}}MB). Lore ipsum: {{maxFilesize}}MB.";
        Dropzone.prototype.defaultOptions.dictResponseError = "Lore ipsum {{statusCode}}.";
        Dropzone.prototype.defaultOptions.dictCancelUpload = "Cancelar";
        Dropzone.prototype.defaultOptions.dictCancelUploadConfirmation = "¿Lore ipsum?";
        Dropzone.prototype.defaultOptions.dictRemoveFile = "Lore ipsum";
        Dropzone.prototype.defaultOptions.dictMaxFilesExceeded = "Lore ipsum {{maxFiles}} Lore ipsum";
        */

        var galleries = document.querySelectorAll('[gallery]');

        galleries.forEach(function (gallery) {

            gallery.classList.add('dropzone');

            var url = gallery.getAttribute('data-url');

            new Dropzone(gallery, {
                url: url,
                addRemoveLinks: true,
                dictRemoveFile: 'Remover',
                init: function() {

                    var dropzone = this;
                    var idGallery = document.getElementById('IdGallery');

                    if (idGallery && idGallery.value) {

                        var request = new Request('/admin/gallery/selectImagesGallery/IdGallery/' + idGallery.value, {
                            headers: new Headers({
                                'Content-Type': 'application/json'
                            }),
                            method: 'GET'
                        });
        
                        fetch(request).then(function (response) {
        
                            if (response.status === 200) {
        
                                return response.json();
        
                            }
                            else {
        
                                throw new Error('Ooops! Internal server error, contact system administrator.');
        
                            }
        
                        })
                        .then(function (result) {
        
                            result.forEach(function (item) {

                                var file = {
                                    name: item.OriginalName,
                                    newName: item.NewName,
                                    size: 0,
                                    dataURL: '/public/uploads/gallery/' + item.NewName,
                                    idGalleryImage: item.IdGalleryImage
                                };

                                dropzone.files.push(file);
                                dropzone.emit('addedfile', file);

                                dropzone.createThumbnailFromUrl(
                                    file, 
                                    dropzone.options.thumbnailWidth, 
                                    dropzone.options.thumbnailHeight,
                                    dropzone.options.thumbnailMethod, 
                                    true, 
                                    function (thumbnail) {
                                        dropzone.emit('thumbnail', file, thumbnail);
                                    }
                                );

                                dropzone.emit('complete', file);

                            });
        
                        });

                    }
            
                },
                removedfile: function(file) {

                    var dropzone = this;

                    var request = new Request('/admin/gallery/deleteImage/IdGalleryImage/' + file.idGalleryImage, {
                        headers: new Headers({
                            'Content-Type': 'application/json'
                        }),
                        method: 'GET'
                    });
    
                    fetch(request).then(function (response) {
    
                        if (response.status === 200) {
    
                            return response.json();
    
                        }
                        else {
    
                            throw new Error('Ooops! Internal server error, contact system administrator.');
    
                        }
    
                    })
                    .then(function (result) {

                        if (result.result) {
                            
                            file.previewElement.remove();

                        }

                    });

                }
                
            });

        });

    })();

};

window.addEventListener('load', AppLoad, true);

/**
 * App funtions
 */

var App = (function () {

    /**
     * Setting SweetAlert2
     */

    var toast = swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 5000
    });

    /**
     * App functions
     */

    /**
     * Toast functions
     */

    this.toast = function (type, title) {

        toast({
            type: type,
            title: title
        })

    };

    this.toastSuccess = function (title) {

        this.toast('success', title);

    };

    this.toastError = function (title) {

        this.toast('error', title);

    };

    this.toastWarning = function (title) {

        this.toast('warning', title);

    };

    this.toastInfo = function (title) {

        this.toast('info', title);

    };

    /**
     * Confirm functions
     */

    this.confirmDialog = function (options) {

        var promise = new Promise(function (resolve, reject) {
       
            swal({
                title: options.title,
                html: options.html,
                type: options.type,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sim, pode deletar!',
                cancelButtonText: 'Cancelar',
                reverseButtons: true,
                allowEnterKey: true
            }).then((result) => {
                
                if (result.value) {
                    
                    resolve(true);
    
                }
                else {
                    
                    resolve(false);

                }
    
            });
    
        });

        promise.on = function (type, callback) {

            this.then(function (result) {

                if (type === 'ok') {

                    if (result) {
                        
                        callback();
    
                    }
                    
                }
                else if (type === 'cancel') {
    
                    if (!result) {
                        
                        callback();
    
                    }
    
                }

            });

            return this;

        };

        return promise;

    };

});

var app = new App();