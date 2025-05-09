<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 text-gray-900">
        <h3 class="text-lg font-medium mb-4">{{ __('Your Referral Link') }}</h3>
        
        <div class="mb-4">
            <p class="mb-2">{{ __('Share this link with your friends and earn rewards when they join!') }}</p>
        </div>
        
        <div class="flex items-center mb-4">
            <input type="text" id="referral-link" value="{{ $referralUrl }}" class="w-full p-2 border border-gray-300 rounded-md mr-2" readonly>
            <button onclick="copyReferralLink()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Copy') }}
            </button>
        </div>
        
        <div id="copy-success" class="hidden p-2 mb-4 bg-green-100 text-green-700 rounded-md">
            {{ __('Link copied to clipboard!') }}
        </div>
        
        <div class="mt-4">
            <h4 class="font-medium mb-2">{{ __('Referral Stats') }}</h4>
            <div class="grid grid-cols-2 gap-4">
                <div class="p-3 bg-gray-50 rounded-md">
                    <p class="text-sm text-gray-600">{{ __('Total Referrals') }}</p>
                    <p class="text-xl font-semibold">{{ $totalReferrals }}</p>
                </div>
                <div class="p-3 bg-gray-50 rounded-md">
                    <p class="text-sm text-gray-600">{{ __('Pending Rewards') }}</p>
                    <p class="text-xl font-semibold">{{ $pendingRewards }} USDT</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyReferralLink() {
    var copyText = document.getElementById("referral-link");
    copyText.select();
    copyText.setSelectionRange(0, 99999); // For mobile devices
    
    navigator.clipboard.writeText(copyText.value);
    
    var copySuccess = document.getElementById("copy-success");
    copySuccess.classList.remove("hidden");
    
    setTimeout(function() {
        copySuccess.classList.add("hidden");
    }, 3000);
}
</script>
