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

$name = document.querySelector('[data-lang="cars_brand_form_translate_' + $locale + '"]');

if($name)
{

    let debouncerepeat = 100;

    setTimeout(function zZFlBXCqXd()
    {

        if(debouncerepeat > 500)
        {
            return;
        }

        if(typeof semanticUrl.debounce === 'function')
        {
            $name.addEventListener('input', semanticUrl.debounce(500));
            return;
        }

        debouncerepeat += 100;

        setTimeout(zZFlBXCqXd, debouncerepeat);

    }, 100);


    function semanticUrl()
    {
        /* Заполняем транслитом URL */
        $semantic = translitRuEn(this.value).toLowerCase();
        document.getElementById('cars_brand_form_info_url').value = $semantic;
    }
}