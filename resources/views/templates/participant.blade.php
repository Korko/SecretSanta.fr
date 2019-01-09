<script type="text/x-template" id="participant-template">
    <tr class="participant" :id="'participant_'+idx">
        <td scope="row">
            <div class="input-group">
                <div class="input-group-prepend counter"><span class="input-group-text">@{{ idx+1 }}</span></div>
                <input type="text" :name="'name['+idx+']'" :required="idx < 3" placeholder="@lang('form.name.placeholder')" v-model="name" class="form-control participant-name" />
            </div>
        </td>
        <td class="border-left">
            <input type="email" :name="'email['+idx+']'" placeholder="@lang('form.email.placeholder')" v-model="email" class="form-control participant-email" :required="(idx < 3 || name !== '') && (!phone || dearsanta)" />
        </td>
        <td>
            @lang('form.mail-sms')
        </td>
        <td class="border-right">
            <div class="input-group">
                <span class="input-group-addon lang" lang="fr">(+33)</span>
                <input type='tel' :disabled="smsdisabled" pattern='0?[67]\s?(\d\s?){8}' :maxlength="(phone[0] === undefined || phone[0] === '0') ? 10 : 9" :name="'phone['+idx+']'" placeholder="@lang('form.phone.placeholder')" v-model="phone" class="form-control participant-phone" :required="(idx < 3 || name !== '') && !email" />
            </div>
        </td>
        <td class="border-right text-left participant-exclusions-wrapper">
            <select2 :name="'exclusions['+idx+'][]'" :options="participantNames" v-model="exclusions" placeholder="@lang('form.exclusions.placeholder')"></select2>
        </td>
        <td class="participant-remove-wrapper">
            <button type="button" class="btn btn-danger participant-remove" :disabled="participants.length <= 3" @click="$emit('delete')">
                <span><i class="fas fa-minus"></i> @lang('form.participant.remove')</span>
            </button>
        </td>
    </tr>
</script>
