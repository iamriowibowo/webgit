<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Projects') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="container">
                        <select name="" id="project-name">
                            <option value="">Select project</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project }}">{{ $project }}</option>                                
                            @endforeach
                        </select>
                        <select name="" id="branch-name">
                        </select>
                        <button class="btn-checkout bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" disabled>pull</button>
                    </div>
                </div>
                <div class="p-6 text-gray-900">
                    <span class="loader">Processing...</span>
                    <div class="checkout-status">
                        <span class="message"></span> <br>
                        <span class="error text-red-600"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    
    @push('scripts')
        <script>
            $(".loader").hide();
            // $(".checkout-status").hide();

            $('#project-name').change(function(){
                var projectName = $(this).val();                
                $("#branch-name").empty();

                // Set checkout button disabled when project name is not selected
                if (!projectName) {
                    $(".btn-checkout").attr("disabled", "disabled");
                    return;
                }

                // Get all branches of selected project
                $.get('projects/' + projectName, function (branches) {

                    // Append them into select option
                    branches.forEach(function(branch) {
                        $('#branch-name').append($('<option>').val(branch).text(branch));
                    });

                    // Remove disabled attribute from button 
                    $(".btn-checkout").removeAttr("disabled");
                });
            });

            $(".btn-checkout").on( "click", function() {
                var projectName = $("#project-name").val();
                var branchName = $("#branch-name").val();

                // Disable checkout button to prevent multiple click
                $(".btn-checkout").attr("disabled", "disabled");

                // Show loader
                $(".loader").show();

                // Hide previous checkout status
                $(".checkout-status").hide();
                $(".checkout-status .error").text('');

                // Checkout project to selected branch
                $.get('projects/' + projectName + '/checkout?branch=' + branchName, function (data) {

                    // Show checkout status
                    $(".checkout-status .message").text(data.message);
                    $(".checkout-status .error").text(data.error);

                    // Hide loader
                    $(".loader").hide();

                    // Show checkout status
                    $(".checkout-status").show();

                    // Repopulate latest checked out  branches of selected project
                    $.get('projects/' + projectName, function (branches) {

                        // Remove all previous branch
                        $("#branch-name").empty();

                        // Append them into select option
                        branches.forEach(function(branch) {
                            $('#branch-name').append($('<option>').val(branch).text(branch));
                        });

                        // Remove disabled attribute from button 
                        $(".btn-checkout").removeAttr("disabled");
                    });                    
                });
            });
        </script>
    @endpush
</x-app-layout>