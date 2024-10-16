/*
 *  Copyright 2023.  Baks.dev <admin@baks.dev>
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

limit = 100;

setTimeout(function initCarFilter()
{

    cars_filter_form_brand = document.getElementById('cars_filter_form_brand');
    cars_filter_form_model = document.getElementById('cars_filter_form_model');

    if(cars_filter_form_brand)
    {

        cars_filter_form_brand.addEventListener('change', changeObjectBrand, false);


        if(cars_filter_form_model.tagName === 'SELECT')
        {
            cars_filter_form_model.addEventListener('change', changeObjectModel, false);
        }

        return;
    }


    if(limit > 1000)
    {
        return;
    }

    setTimeout(initCarFilter, limit++);

}, 100);


function changeObjectBrand()
{

    let replaceId = 'cars_filter_form_model';

    /* Создаём объект класса XMLHttpRequest */
    const requestModalName = new XMLHttpRequest();
    requestModalName.responseType = "document";

    /* Имя формы */
    let carFilterForm = document.forms.cars_filter_form;
    let formData = new FormData();
    formData.append(this.getAttribute('name'), this.value);

    requestModalName.open(carFilterForm.getAttribute('method'), carFilterForm.getAttribute('action'), true);

    /* Указываем заголовки для сервера */
    requestModalName.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

    /* Получаем ответ от сервера на запрос*/
    requestModalName.addEventListener("readystatechange", function()
    {
        /* request.readyState - возвращает текущее состояние объекта XHR(XMLHttpRequest) */
        if(requestModalName.readyState === 4 && requestModalName.status === 200)
        {

            (requestModalName.response
                .getElementById('cars_filter_form_model'))?.classList.remove('is-invalid');

            (requestModalName.response
                .getElementById('cars_filter_form_modification'))?.classList.remove('is-invalid');

            let result = requestModalName.response.getElementById(replaceId);

            document.getElementById(replaceId).replaceWith(result);

            /* Удаляем предыдущий Select2 */
            let select2 = document.getElementById(replaceId + '_select2');


            if(select2)
            {
                select2.remove();
            }


            let replacer = document.getElementById(replaceId);

            if(result.dataset.select === 'select2' && replacer.tagName === 'SELECT')
            {
                new NiceSelect(replacer, {searchable: true, id: 'select2-' + replaceId});

                /** Событие на изменение торгового предложения */
                let offerChange = document.getElementById(replaceId);

                if(offerChange)
                {
                    offerChange.addEventListener('change', changeObjectModel, false);
                }
            } else
            {
                replacer.addEventListener('change', changeObjectModel, false);
            }


            let resetId = 'cars_filter_form_modification';
            let reset = requestModalName.response.getElementById(resetId);
            document.getElementById(resetId).replaceWith(reset);

            /* Удаляем предыдущий Select2 */
            let reset2 = document.getElementById(resetId + '_select2');

            if(reset2)
            {
                reset2.remove();
            }
        }

        return false;
    });

    requestModalName.send(formData);
}

function changeObjectModel()
{

    let replaceId = 'cars_filter_form_modification';

    /* Создаём объект класса XMLHttpRequest */
    const requestModalName = new XMLHttpRequest();
    requestModalName.responseType = "document";

    /* Имя формы */
    let carFilterForm = document.forms.cars_filter_form;
    let formData = new FormData();
    formData.append(this.getAttribute('name'), this.value);

    requestModalName.open(carFilterForm.getAttribute('method'), carFilterForm.getAttribute('action'), true);

    /* Указываем заголовки для сервера */
    requestModalName.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

    /* Получаем ответ от сервера на запрос*/
    requestModalName.addEventListener("readystatechange", function()
    {
        /* request.readyState - возвращает текущее состояние объекта XHR(XMLHttpRequest) */
        if(requestModalName.readyState === 4 && requestModalName.status === 200)
        {

            (requestModalName.response
                .getElementById('cars_filter_form_modification'))?.classList.remove('is-invalid');

            let result = requestModalName.response.getElementById(replaceId);

            document.getElementById(replaceId).replaceWith(result);

            /* Удаляем предыдущий Select2 */
            let select2 = document.getElementById(replaceId + '_select2');

            if(select2)
            {
                select2.remove();
            }

            let replacer = document.getElementById(replaceId);

            if(result.dataset.select === 'select2' && replacer.tagName === 'SELECT')
            {
                new NiceSelect(replacer, {searchable: true, id: 'select2-' + replaceId});

                /** Событие на изменение торгового предложения */
                let offerChange = document.getElementById(replaceId);

                // if (offerChange) {
                //     offerChange.addEventListener('change', changeObjectModel, false);
                // }
            }
            // else
            // {
            //     replacer.addEventListener('change', changeObjectModel, false);
            // }

        }

        return false;
    });

    requestModalName.send(formData);
}

//
//
// const form = document.forms.cars_filter_form;
// const form_select_sport = document.getElementById('cars_filter_form_brand');
// const form_select_position = document.getElementById('cars_filter_form_model');
//
// const updateForm = async (data, url, method) => {
//     const req = await fetch(url, {
//         method: method,
//         body: data,
//         headers: {
//             'Content-Type': 'application/x-www-form-urlencoded',
//             'charset': 'utf-8'
//         }
//     });
//
//     const text = await req.text();
//
//     return text;
// };
//
// const parseTextToHtml = (text) => {
//     const parser = new DOMParser();
//     const html = parser.parseFromString(text, 'text/html');
//
//     return html;
// };
//
// const changeOptions = async (e) => {
//     const requestBody = e.target.getAttribute('name') + '=' + e.target.value;
//     const updateFormResponse = await updateForm(requestBody, form.getAttribute('action'), form.getAttribute('method'));
//     const html = parseTextToHtml(updateFormResponse);
//
//     const new_form_select_position = html.getElementById('meetup_position');
//     form_select_position.innerHTML = new_form_select_position.innerHTML;
// };
//
// form_select_sport.addEventListener('change', (e) => changeOptions(e));
