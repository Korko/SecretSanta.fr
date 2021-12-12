<script>
    export default {
        props: {
            labelYes: {
                type: String,
                default: 'Yes'
            },
            backgroundYes: {
                type: String,
                default: '#67a5ec'
            },
            labelNo: {
                type: String,
                default: 'No'
            },
            backgroundNo: {
                type: String,
                default: '#ccc'
            },
            id: {
                type: String,
                default() {
                    return '' + Math.floor(Math.random() * Date.now());
                }
            },
            name: {
                type: String,
                default: null
            },
            value: {
                type: Boolean,
                default: false
            }
        }
    }
</script>

<template>
    <span>
        <input type="checkbox" class="toggle" :id="id" :name="name" :checked="value" value="1" @change="$emit('input', $event.target.checked)" />
        <label :for="id" :style="{'--backgroundNo': backgroundNo, '--backgroundYes': backgroundYes}">
          <span class="on">{{ labelYes }}</span>
          <span class="off">{{ labelNo }}</span>
        </label>
    </span>
</template>

<style lang="scss" scoped>
    input[type="checkbox"] {

      &.toggle {
        opacity: 0;
        position: absolute;
        left: -99999px;

        & + label {
          height: 40px;
          line-height: 40px;
          background-color: var(--backgroundNo);
          padding: 0px 16px;
          border-radius: 16px;
          display: inline-block;
          position: relative;
          cursor: pointer;
          transition: all .25s ease-in;
          box-shadow: inset 0px 0px 2px rgba(0,0,0, .5);

          &:before, &:hover:before {
            content: ' ';
            position: absolute;
            top: 2px;
            left: 2px;
            width: 46px;
            height: 36px;
            background: #fff;
            z-index: 2;
            transition: all .25s ease-in;
            border-radius: 14px;
          }
          .off, .on {
            color: #fff;
          }
          .off {
            margin-left: 46px;
            display:inline-block;
          }
          .on {
            display: none;
          }
        }

        &:checked + label {
          .off {
            display: none;
          }
          .on {
            margin-right: 46px;
            display:inline-block;
          }
        }

        &:checked + label, &:focus:checked + label {
          background-color: var(--backgroundYes);

          &:before, &:hover:before {
            background-position: 0 0;
            top: 2px;
            left: 100%;
            margin-left:-48px;
          }
        }
      }
    }
    // Below is for display only
    body {
      background: #f1f1f1;
      padding-top: 24px;
      text-align:center;
      font-family: arial;
    }
    p {
      &:first-of-type {
        margin-top:24px;
      }
      font-size: 16px;
      color: #717171;
      margin:0; 
    }
</style>