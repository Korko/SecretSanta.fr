<x-layouts.main>
    @pushOnce('head')
        @vite(['resources/sass/randomForm.scss', 'resources/js/randomForm.js'])
    @endPushOnce

    <div id="header">
        <div class="bg-overlay"></div>
        <div class="center text-center">
            <div class="banner">
                <h1>@lang('form.title')</h1>
            </div>
            <div class="subtitle">
                <h4>@lang('form.subtitle')</h4>
            </div>
        </div>
        <div class="bottom text-center">
            <a id="scrollDownArrow" @click.prevent="scrollTo('#section-what', 1000)">
                <i class="fa fa-chevron-down"></i>
            </a>
        </div>
    </div>

    <a name="what" id="what" />
    <div class="light-wrapper">
        <section class="ss-style-top"></section>
        <div class="container inner">
            <h2 class="section-title text-center">
                @lang('form.section.what.title')
            </h2>
            <p class="lead main text-center">
                @lang('form.section.what.subtitle')
            </p>
            <div class="row text-center what">
                <ul class="media-list w-100">
                    <li class="media media-icon-left media-calendar-icon">
                        <div class="media-body">
                            <h4 class="media-heading">
                                @lang('form.section.what.heading1')
                            </h4>
                            <p class="paragraph">
                                @lang('form.section.what.content1')
                            </p>
                        </div>
                    </li>
                </ul>
                <div class="card w-100">
                    <div class="bg-light card-body">
                        <h6 class="card-title">
                            @lang('form.fyi')
                        </h6>
                        <p class="card-text">
                            <p class="paragraph">
                                @lang('form.section.what.notice')
                            </p>
                            <x-buy-me-coffee />
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <section class="ss-style-bottom"></section>
    </div>

    <div class="parallax">
        <div class="container inner"></div>
    </div>

    <a name="how" id="how" />
    <div class="light-wrapper">
        <section class="ss-style-top"></section>
        <div class="container inner">
            <h2 class="section-title text-center">
                @lang('form.section.how.title')
            </h2>
            <p class="lead main text-center">
                @lang('form.section.how.subtitle')
            </p>
            <div class="row text-center how">
                <ul class="media-list w-100">
                    <li class="media media-icon-left media-user-icon">
                        <div class="media-body">
                            <h4 class="media-heading">
                                @lang('form.section.how.heading1')
                            </h4>
                            <p class="paragraph">
                                @lang('form.section.how.content1')
                            </p>
                        </div>
                    </li>
                    <li class="media media-icon-right media-paper-icon">
                        <div class="media-body">
                            <h4 class="media-heading">
                                @lang('form.section.how.heading2')
                            </h4>
                            <p class="paragraph">
                                @lang('form.section.how.content2')
                            </p>
                        </div>
                    </li>
                    <li class="media media-icon-left media-mail-icon">
                        <div class="media-body">
                            <h4 class="media-heading">
                                @lang('form.section.how.heading3')
                            </h4>
                            <p class="paragraph">
                                @lang('form.section.how.content3')
                            </p>
                        </div>
                    </li>
                    <li class="media media-icon-right media-clock-icon">
                        <div class="media-body">
                            <h4 class="media-heading">
                                @lang('form.section.how.heading4')
                            </h4>
                            <p class="paragraph">
                                @lang('form.section.how.content4')
                            </p>
                        </div>
                    </li>
                </ul>
                <div class="card w-100">
                    <div class="bg-light card-body">
                        <h6 class="card-title">
                            @lang('form.fyi')
                        </h6>
                        <p class="card-text paragraph">
                            @lang('form.section.how.notice', [
                                'sources' => '<a href="https://framagit.org/Korko/SecretSanta/">GitLab</a>'
                            ])
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <section class="ss-style-bottom"></section>
    </div>

    <div class="parallax parallax2">
        <div class="container inner"></div>
    </div>

    <a name="form" id="form" />
    <div class="light-wrapper">
        <section class="ss-style-top"></section>
        <div class="container inner" id="randomForm">
            <h2 class="section-title text-center">
                @lang('form.section.go.title')
            </h2>
            <p class="lead main text-center">
                @lang('form.section.go.subtitle')
            </p>
            <div class="row text-center form">
                <form method="post" action="{{ route('form.process') }}" autocomplete="off" v-on:submit.prevent="submit">
                    <fieldset>
                        <div v-cloak v-show="sent" id="success-wrapper" class="alert alert-success">
                            @lang('form.success')
                            <div v-if="draw_status === 'created'">
                                @lang('form.status.created')
                            </div>
                            <div v-else-if="draw_status === 'drawing'">
                                @lang('form.status.drawing')
                            </div>
                            <div v-else-if="draw_status === 'drawn'">
                                @lang('form.status.drawn')
                            </div>
                        </div>

                        <toggle
                            class="organizerToggle"
                            name="participant-organizer"
                            v-model="form['participant-organizer']"
                            label-yes="@lang('form.organizerIn')"
                            label-no="@lang('form.organizerOut')"
                            background-yes="#0069D9"
                            background-no="#B50119"
                        ></toggle>
                    </fieldset>

                    <fieldset v-if="!form['participant-organizer']" id="organizer">
                        <legend>@lang('form.organizer.title')</legend>
                        <div class="table-responsive form-group">
                            <table class="table table-hover table-numbered">
                                <caption>@lang('form.organizer.title')</caption>
                                <tbody>
                                    <tr>
                                        <th style="width: 33%" scope="col">
                                            @lang('form.organizer.name')
                                        </th>
                                        <td>
                                            <div class="input-group">
                                                <input
                                                    type="text"
                                                    name="organizer[name]"
                                                    v-model="form.organizer.name"
                                                    v-on:change="form.validate('organizer.name')"
                                                    placeholder="@lang('form.participant.name.placeholder')"
                                                    class="form-control participant-name"
                                                    v-bind:class="{ 'is-invalid': form.invalid('organizer.name') }"
                                                    v-bind:aria-invalid="form.invalid('organizer.name')"
                                                ></input>
                                                <div v-if="form.invalid('organizer.name')" class="invalid-feedback">
                                                    @{{ form.errors['organizer.name'] }}
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="width: 33%" scope="col">
                                            @lang('form.organizer.email')
                                        </th>
                                        <td>
                                            <div class="input-group">
                                                <input
                                                    type="email"
                                                    name="organizer[email]"
                                                    v-model="form.organizer.email"
                                                    v-on:change="form.validate('organizer.email')"
                                                    placeholder="@lang('form.participant.email.placeholder')"
                                                    class="form-control participant-email"
                                                    v-bind:class="{ 'is-invalid': form.invalid('organizer.email') }"
                                                    v-bind:aria-invalid="form.invalid('organizer.email')"
                                                ></input>
                                                <div v-if="form.invalid('organizer.email')" class="invalid-feedback">
                                                    @{{ form.errors['organizer.email'] }}
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </fieldset>

                    <fieldset id="participants">
                        <legend>@lang('form.participants.title')</legend>
                        <div class="table-responsive form-group">
                            <table class="table table-hover table-numbered">
                                <caption>@lang('form.participants.caption')</caption>
                                <thead>
                                    <tr>
                                        <th style="width: 33%" scope="col">
                                            @lang('form.participant.name.label')
                                        </th>
                                        <th style="width: 33%" scope="col">
                                            @lang('form.participant.email.label')
                                        </th>
                                        <th style="width: 30%" scope="col">
                                            @lang('form.participant.exclusions.label')
                                        </th>
                                        <th style="width: 3%" scope="col" />
                                    </tr>
                                </thead>
                                <tbody is="vue:TransitionGroup" type="transition" name="fade">
                                    <!-- Default is three empty rows to have three entries at any time -->
                                    <tr v-for="(participant, idx) in form.participants" class="participant" v-bind:key="idx">
                                        <td class="align-middle">
                                            <div class="input-group">
                                                <span class="input-group-prepend counter">
                                                    <span class="input-group-text"
                                                        >@{{ idx + 1 }}<template v-if="idx === 0 && form['participant-organizer']"> - @lang('form.participant.organizer')</template></span
                                                    >
                                                </span>
                                                <input
                                                    type="text"
                                                    v-bind:name="'participants[' + idx + '][name]'"
                                                    v-model="form.participants[idx].name"
                                                    v-on:change="form.validate(`participants.${idx}.name`)" {{-- Beware to use participants.idx.name and not participants[idx].name, else Precognition will not trigger validation --}}
                                                    placeholder="@lang('form.participant.name.placeholder')"
                                                    class="form-control participant-name"
                                                    v-bind:class="{ 'is-invalid': form.invalid(`participants.${idx}.name`) }"
                                                    v-bind:aria-invalid="form.invalid(`participants.${idx}.name`)"
                                                ></input>
                                                <div v-if="form.invalid(`participants.${idx}.name`)" class="invalid-feedback">
                                                    @{{ form.errors[`participants.${idx}.name`] }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="border-left align-middle">
                                            <div class="input-group">
                                                <input
                                                    type="email"
                                                    v-bind:name="'participants[' + idx + '][email]'"
                                                    v-model="form.participants[idx].email"
                                                    v-on:change="form.validate(`participants.${idx}.email`)"
                                                    placeholder="@lang('form.participant.email.placeholder')"
                                                    class="form-control participant-email"
                                                    v-bind:class="{ 'is-invalid': form.invalid(`participants.${idx}.email`) }"
                                                    v-bind:aria-invalid="form.invalid(`participants.${idx}.email`)"
                                                ></input>
                                                <div v-if="form.invalid(`participants.${idx}.email`)" class="invalid-feedback">
                                                    @{{ form.errors[`participants.${idx}.email`] }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="border-right text-left participant-exclusions-wrapper align-middle">
                                            <multiselect
                                                v-bind:options="otherParticipants(idx)"
                                                label="name"
                                                track-by="name"
                                                value-prop="idx"
                                                v-bind:value="form.participants[idx].exclusions"
                                                placeholder="@lang('form.participant.exclusions.placeholder')"
                                                v-bind:multiple="true"
                                                v-bind:hide-selected="true"
                                                v-bind:searchable="true"
                                                v-bind:strict="false"
                                                mode="tags"
                                                v-bind:close-on-select="true"
                                                no-options-text="@lang('form.participant.exclusions.noOptions')"
                                                no-results-text="@lang('form.participant.exclusions.noResult')"
                                            ></multiselect>
                                            <select style="display:none" v-bind:name="'participants[' + idx + '][exclusions][]'" multiple>
                                                <option
                                                    v-for="exclusion in form.participants[idx].exclusions"
                                                    v-bind:key="exclusion"
                                                    selected
                                                >@{{ exclusion }}</option>
                                            </select>
                                        </td>
                                        <td class="participant-remove-wrapper align-middle">
                                            <button
                                                type="button"
                                                class="btn btn-outline-danger participant-remove"
                                                v-bind:disabled="idx < 3 && form.participants.length <= 3"
                                                v-on:click="form.participants.splice(idx, 1)"
                                            >
                                                <i class="fas fa-minus"></i> <span> @lang('form.participant.remove')</span>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-outline-success participant-add" v-on:click="addParticipant()">
                                <i class="fas fa-plus"></i>
                                @lang('form.participant.add')
                            </button>
                            <button
                                v-cloak {{-- CSV import needs JS --}}
                                type="button"
                                class="btn btn-outline-warning participants-import"
                                v-bind:disabled="importing"
                                v-on:click="showModal = true"
                            >
                                <span v-if="importing"><i class="fas fa-spinner fa-spin"></i> @lang('form.participants.importing')</span>
                                <span v-else><i class="fas fa-list-alt"></i> @lang('form.participants.import')</span>
                            </button>
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend>Messages</legend>
                        <div id="contact">
                            <fieldset id="form-mail-group">
                                <div class="form-group">
                                    <label for="mailTitle">@lang('form.mail.title.label')</label>
                                    <div class="input-group">
                                        <input
                                            id="mailTitle"
                                            type="text"
                                            name="title"
                                            v-model="form.title"
                                            v-on:change="form.validate('title')"
                                            placeholder="@lang('form.mail.title.placeholder')"
                                            class="form-control"
                                            v-bind:class="{ 'is-invalid': form.invalid('title') }"
                                            v-bind:aria-invalid="form.invalid('title')"
                                        ></input>
                                        <div v-if="form.invalid('title')" class="invalid-feedback">
                                            @{{ form.errors.title }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="mailContent">@lang('form.mail.content.label')</label>
                                    <div class="input-group">
                                        <textarea
                                            id="mailContent"
                                            name="content"
                                            v-model="form.content"
                                            v-on:change="form.validate('content')"
                                            placeholder="@lang('form.mail.content.placeholder')"
                                            class="form-control"
                                            v-bind:class="{ 'is-invalid': form.errors.content }"
                                            v-bind:aria-invalid="form.errors.content"
                                            style="width: 100%;"
                                            v-bind:rows="form.content.split('\n').length + 1"
                                        ></textarea>
                                        <div v-if="form.errors.content" class="invalid-feedback">
                                            @{{ form.errors.content }}
                                        </div>
                                    </div>
                                    <textarea
                                        id="mailPost"
                                        class="form-control extended"
                                        read-only
                                        disabled
                                        value="@lang('form.mail.post')"
                                        rows="{{ count(explode(PHP_EOL, __('form.mail.post'))) }}"
                                    ></textarea>
                                </div>
                            </fieldset>
                        </div>
                    </fieldset>

                    <fieldset>
                        <button type="submit" class="btn btn-primary btn-lg" v-bind:disabled="sent || sending || form.validating || form.hasErrors">
                            <span v-cloak v-if="sent"><i class="fas fa-check-circle"></i> @lang('common.form.sent')</span>
                            <span v-cloak v-else-if="sending"><i class="fas fa-spinner"></i> @lang('common.form.sending')</span>
                            <span v-cloak v-else-if="form.validating"><i class="fas fa-spinner"></i> @lang('common.form.validating')</span>
                            <span v-cloak v-else-if="form.hasErrors"><i class="fas fa-lock"></i> @lang('common.form.hasErrors')</span>
                            <span v-else><i class="fas fa-dice"></i> @lang('form.submit')</span>
                        </button>
                        {{-- <button v-if="sent" type="reset" class="btn btn-primary btn-lg">
                            <span><span class="fas fa-backward" /> {{ buttonReset || $t('common.form.reset') }}</span>
                        </button> --}}
                    </fieldset>
                </form>
                <csv v-cloak v-if="showModal" v-on:import="importParticipants" v-on:close="showModal = false"></csv>
            </div>
        </div>
        </div>
        <section class="ss-style-bottom"></section>
    </div>
</x-layouts.main>
