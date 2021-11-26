import { h } from 'vue';

export default {
    mounted: function (el, binding, vnode) {
        el.className = (el.className + " tip-handler").trim();

        var direction =
            binding.modifiers.top ? 'top' :
            (binding.modifiers.left ? 'left' :
            (binding.modifiers.bottom ? 'bottom' :
            'right'));

        var div = document.createElement('div');
        div.className = 'tip-wrapper';

        // Replace the element by the div and add the element in the div
        el.parentNode.insertBefore(div, el);
        div.appendChild(el);

        if (binding.value.img)
            if (typeof binding.value.img === 'object' || binding.value.img instanceof Object) {
                var img = h('picture');
                Object.keys(binding.value.img).forEach(mime => {
                    img += '<source srcset="' + binding.value.img[mime] + '" type="' + mime + '">';
                });
                img += '<img class="media-object" src="' + Object.values(binding.value.img).slice(-1)[0] + '">';
                img += '</picture>';
                binding.value.img = img;
            } else {
                binding.value.img = '<img src="' + binding.value.img + '" />';
            }

        var div2 = document.createElement('div');
        div2.className = "tip-content " + direction;
        div2.innerHTML = binding.value.img
            + '<div class="text-content">' + (binding.value.text || binding.value) + '</div>'
            + '<i></i>';
        div.appendChild(div2);
    }
};