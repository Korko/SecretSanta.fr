import { h } from 'vue';

export default {
    mounted: function (el, binding, vnode) {
        el.className = (el.className + " tip-handler").trim();

        var direction =
            binding.modifiers.top ? 'top' :
            (binding.modifiers.left ? 'left' :
            (binding.modifiers.bottom ? 'bottom' :
            'right'));

        var div = <div class="tip-wrapper"></div>;

        // Replace the element by the div and add the element in the div
        el.parentNode.insertBefore(div, el);
        div.appendChild(el);

        if (binding.value.img) {
            if (typeof binding.value.img === 'object' || binding.value.img instanceof Object) {
                binding.value.img = <picture>
                    {
                        Object.keys(binding.value.img).map(mime => {
                            return <source srcset={binding.value.img[mime]} type={mime} />;
                        })
                    }
                    <img class="media-object" src={Object.values(binding.value.img).slice(-1)[0]} />
                </picture>;
            } else {
                binding.value.img = <img src={binding.value.img} />;
            }
        }

        div.appendChild(
            <div class={"tip-content " + direction}>
                {binding.value.img}
                <div class="text-content">{ binding.value.text || binding.value }</div>
                <i></i>
            </div>
        );
    }
};