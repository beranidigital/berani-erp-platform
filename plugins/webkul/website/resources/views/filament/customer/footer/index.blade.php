  <footer class="px-6 py-12 bg-black text-white">
		<div class="container max-w-6xl mx-auto">
			<div class="grid grid-cols-1 gap-8 md:grid-cols-3">
				<!-- Logo and Description Column -->
				<div class="md:col-span-1">
					<div class="mb-6">
						<a href="{{ url('/') }}">
							<x-filament-panels::logo />
						</a>
					</div>

					<p class="mb-4 text-white/80">
						Designed to optimize and streamline business operations, Aureus ERP is suitable for enterprises of all sizes.
					</p>

					<p class="text-white/80">
						The platform emphasizes reporting for insights, security, localization flexibility, and integration with CRMs, BI tools, and APIs.
					</p>
				</div>

				<!-- Useful Links Column -->
				<div class="md:col-span-1">
					<h3 class="mb-4 text-lg font-medium text-white">Useful Links</h3>

					<ul class="space-y-2">
						@foreach ($navigationItems as $item)
							<li>
								<a href="{{ $item->getUrl() }}" class="text-white/80 underline decoration-white/30 decoration-dotted underline-offset-4 transition hover:text-white hover:decoration-white">
									{{ $item->getLabel() }}
								</a>
							</li>
						@endforeach
					</ul>
				</div>

				<!-- Contact and Social Media Column -->
				<div class="md:col-span-1">
					@if (isset($contacts['email']) && isset($contacts['phone']))
						<h3 class="mb-4 text-lg font-medium text-white">Contact Us</h3>

						@if (isset($contacts['email']))
							<div class="mb-2">
								<a href="mailto:{{ $contacts['email'] }}" class="flex items-center text-white/80 hover:text-white">
									<x-filament::icon
										icon="heroicon-m-envelope"
										class="w-5 h-5 mr-2"
									/>

									{{ $contacts['email'] }}
								</a>
							</div>
						@endif

						@if (isset($contacts['phone']))
							<div class="mb-6">
								<a href="tel:{{ $contacts['phone'] }}" class="flex items-center text-white/80 hover:text-white">
									<x-filament::icon
										icon="heroicon-m-phone"
										class="w-5 h-5 mr-2"
									/>

									{{ $contacts['phone'] }}
								</a>
							</div>
						@endif
					@endif

					@if (! $socialLinks->isEmpty())
						<h3 class="mb-4 text-lg font-medium text-white">Follow Us</h3>

						<div class="flex flex-wrap gap-2">
							@foreach ($socialLinks as $item)
								<a
									href="{{ $item->getUrl() }}"
									class="p-2 text-white bg-[#B21518] rounded-full transition hover:bg-[#d62b2f]"
									target="_blank"
								>
                                    {!! $item->getIcon() !!}
								</a>
							@endforeach
						</div>
					@endif
				</div>
			</div>

			<!-- Copyright Section -->
			<div class="flex flex-col justify-between pt-8 mt-8 border-t border-white/20 md:flex-row">
				<div class="text-sm text-white/70">
					Copyright © <a href="https://wiki.beranidigital.id/" class="text-white underline decoration-white/30 decoration-dotted underline-offset-4 hover:text-white hover:decoration-white" target="_blank">BeraniERP</a>
				</div>

			</div>
		</div>
  </footer>


