@extends('layouts.guest')

@section('content')
    <section class="layout-authentication">
                <div>
                    <h1>Log in</h1>
                    <form class="help" action="{{ route('login') }}" method="POST">
                        @csrf
                        <fieldset>
                            <legend>Login-gegevens:</legend>
                            <label for="name">Gebruikersnaam</label>
                            <div>
                                <input
                                    id="name"
                                    name="name"
                                    placeholder="Gebruikersnaam"
                                    type="text">
                            </div>

                            <label for="password">Wachtwoord</label>
                            <div>
                                <input
                                    id="password"
                                    name="password"
                                    
                                    placeholder="wachtwoord"
                                    type="text"
                                    aria-describedby="password-message">
                                <p
                                    class="explanation"
                                    data-open-label="Toelichting bij het veld: Voorbeeld text input"
                                    data-close-label="Sluit toelichting bij het veld: Voorbeeld text input"
                                    id="password-message"
                                ><span>Toelichting:</span> Wachtwoord moet minimaal 8 tekens bevatten waarvan minimaal 1 hoofdletter, 1 kleineletter en 1 cijfer.</p>
                            </div>
                        </fieldset>
                        <button type="submit">Inloggen</button>
                    </form>
                </div>
    </section>
@endsection
