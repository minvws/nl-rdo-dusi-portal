@extends('layouts.app')

@section('content')
    <article>
        <div class="horizontal-scroll">
            <table>
                <caption>Application</caption>
                <thead>
                    <tr>
                        <th colspan="2">Application Details</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>ID</td>
                        <td>{{ $application->id }}</td>
                    </tr>
                    <tr>
                        <td>Created At</td>
                        <td>{{ $application->created_at }}</td>
                    </tr>
                    <tr>
                        <td>Form ID</td>
                        <td>{{ $application->form_id }}</td>
                    </tr>
                    <tr>
                        <td>Locked From</td>
                        <td>{{ $application->locked_from }}</td>
                    </tr>
                    <tr>
                        <td>Judgement</td>
                        <td>{{ $application->judgement }}</td>
                    </tr>
                    @if ($application->judgement == 'rejected')
                        <tr>
                            <td>Reason for Rejection</td>
                            <td>{{ $application->applicationReviews->last()->encrypted_comment }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="horizontal-scroll">
            <table>
                <tr>
                    <th>Question</th>
                    <th>Answer</th>
                </tr>
                @foreach ($answers as $answer)
                    <tr>
                        <td>{{ $answer->field->code }}</td>
                        <td>{{ $answer->encrypted_answer }}</td>
                    </tr>
                @endforeach
            </table>
        </div>

        <h1>Set judgment</h1>
        <p>Current judgment: {{ $application->judgement }}</p>

        <div class="container">
            <form action="{{ route('applications.update', $application->id) }}" method="post">
                @csrf
                <label for="judgement-select">Set judgment</label>
                <select id="judgement-select" name="judgement-select" onchange="showReasonTextbox()">
                    <option value="approved">Approve</option>
                    <option value="rejected" selected>Reject</option>
                </select>

                <div id="reason-container" style="display: block;">
                    <label for="reason">Reason for Rejection</label>
                    <textarea id="reason" name="reason" placeholder="Enter reason for rejection" required></textarea>
                </div>

                <button type="submit" id="judgement-submit" onclick="confirmJudgment()">Send</button>
            </form>
        </div>
        <script>
            function showReasonTextbox() {
                var selectBox = document.getElementById("judgement-select");
                var reasonContainer = document.getElementById("reason-container");

                if (selectBox.value === "rejected") {
                    reasonContainer.style.display = "block";
                    reason.required = true;
                } else {
                    reasonContainer.style.display = "none";
                    reason.required = false;
                }
            }

            function confirmJudgment() {
                var selectBox = document.getElementById("judgement-select");
                var confirmation = confirm("Are you sure you want to submit the judgment as '" + selectBox.value + "'?");

                if (!confirmation) {
                    event.preventDefault();
                }
            }
        </script>
    </article>
@endsection


