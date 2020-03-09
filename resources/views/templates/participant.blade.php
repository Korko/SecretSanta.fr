<script type="text/x-template" id="participant-template">
    <tr class="participant" :id="'participant_'+idx">
        <td class="align-middle">
            <div class="input-group">
                <span class="input-group-prepend counter">
                    <span class="input-group-text">@{{ idx+1 }}<template v-if="idx === 0"> - Organisateur</template></span>
                </span>
                <input type="text" :name="'participants['+idx+'][name]'" :required="idx < 3" placeholder="@lang('form.name.placeholder')" v-model="name" class="form-control participant-name" />
            </div>
        </td>
        <td class="border-left align-middle">
            <input type="email" :name="'participants['+idx+'][email]'" placeholder="@lang('form.email.placeholder')" v-model="email" class="form-control participant-email" :required="(idx < 3 || name !== '')" />
        </td>
        <td class="border-right text-left participant-exclusions-wrapper align-middle">
            <multiselect :options="participantNames" v-model="exclusions" track-by="value" label="text" placeholder="@lang('form.exclusions.placeholder')" :multiple="true"></multiselect>
            <select style="display:none" :name="'participants['+idx+'][exclusions][]'" multiple>
                <option v-for="participantName in participantNames" :value="participantName.value" :selected="exclusions.find(a => (a.value === participantName.value))"></option>
            </select>
        </td>
        <td class="participant-remove-wrapper align-middle">
            <button type="button" class="btn btn-danger participant-remove" :disabled="participants.length <= 3" @click="$emit('delete')">
                <i class="fas fa-minus"></i><span> @lang('form.participant.remove')</span>
            </button>
        </td>
    </tr>
</script>
