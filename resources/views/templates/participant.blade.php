<script type="text/x-template" id="participant-template">
    <tr class="participant" :id="'participant_'+idx">
        <td class="row">
            <div class="input-group">
                <span class="input-group-addon counter">@{{ idx+1 }}</span>
                <input type="text" :name="'participants['+idx+'][name]'" :required="idx < 3" placeholder="@lang('form.name.placeholder')" v-model="name" class="form-control participant-name" />
            </div>
        </td>
        <td class="row border-left">
            <input type="email" :name="'participants['+idx+'][email]'" placeholder="@lang('form.email.placeholder')" v-model="email" class="form-control participant-email" :required="(idx < 3 || name !== '') && (!phone || dearsanta)" />
        </td>
        <td>
            @lang('form.mail-sms')
        </td>
        <td class="row border-right">
            <div class="input-group">
                <span class="input-group-addon lang" lang="fr">(+33)</span>
                <input type='tel' :disabled="smsdisabled" pattern='0?[67]\s?(\d\s?){8}' :maxlength="(phone[0] === undefined || phone[0] === '0') ? 10 : 9" :name="'participants['+idx+'][phone]'" placeholder="@lang('form.phone.placeholder')" v-model="phone" class="form-control participant-phone" :required="(idx < 3 || name !== '') && !email" />
            </div>
        </td>
        <td class="row border-right text-left participant-exclusions-wrapper">
            <select2 :name="'participants['+idx+'][exclusions]'" :options="participantNames" v-model="exclusions" placeholder="@lang('form.exclusions.placeholder')"></select2>
        </td>
        <td class="row participant-remove-wrapper">
            <button type="button" class="btn btn-danger participant-remove" :disabled="participants.length <= 3" @click="$emit('delete')">
                <span class="glyphicon glyphicon-minus"></span><span> @lang('form.participant.remove')</span>
            </button>
        </td>
    </tr>
</script>
