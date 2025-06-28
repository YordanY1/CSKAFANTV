<div class="mt-6">
    <div x-data="tacticBoard()" x-init="init()" class="space-y-6">
        <div id="tactic-wrapper" class="space-y-6 py-8"
            :class="document.fullscreenElement ? 'w-screen h-screen overflow-auto bg-red-700 p-8' : 'bg-red-700'">


            <div class="max-w-4xl mx-auto w-full space-y-4">

                <div class="flex flex-wrap gap-2">
                    <button @click="setTool(null)"
                        :class="tool === null ?
                            'bg-yellow-100 text-yellow-800 border-yellow-300 ring-2 ring-yellow-200' :
                            'bg-white text-gray-700'"
                        class="border px-3 py-2 rounded text-sm hover:bg-yellow-50 transition">
                        🎯 Избиране на играчи
                    </button>

                    <button @click="setTool('draw')"
                        :class="tool === 'draw'
                            ?
                            'bg-blue-100 text-blue-800 border-blue-300 ring-2 ring-blue-200' :
                            'bg-white text-gray-700'"
                        class="border px-3 py-2 rounded text-sm hover:bg-blue-50 transition">
                        ✏️ Чертай
                    </button>

                    <button @click="setTool('arrow')"
                        :class="tool === 'arrow'
                            ?
                            'bg-green-100 text-green-800 border-green-300 ring-2 ring-green-200' :
                            'bg-white text-gray-700'"
                        class="border px-3 py-2 rounded text-sm hover:bg-green-50 transition">
                        ➡️ Стрелка
                    </button>

                    <button @click="setTool('eraser')"
                        :class="tool === 'eraser'
                            ?
                            'bg-red-100 text-red-800 border-red-300 ring-2 ring-red-200' :
                            'bg-white text-gray-700'"
                        class="border px-3 py-2 rounded text-sm hover:bg-red-50 transition">
                        🧽 Гума
                    </button>

                    <button @click="clearBoard"
                        class="ml-auto text-sm text-white bg-red-600 border border-red-800 px-4 py-2 rounded hover:bg-red-700 transition">
                        🧹 Изчисти дъската
                    </button>


                    <button @click="setTool('ball')"
                        :class="tool === 'ball'
                            ?
                            'bg-orange-100 text-orange-800 border-orange-300 ring-2 ring-orange-200' :
                            'bg-white text-gray-700'"
                        class="border px-3 py-2 rounded text-sm hover:bg-orange-50 transition">
                        ⚽ Добави топка
                    </button>

                    <button @click="toggleFullscreen"
                        class="border px-3 py-2 rounded text-sm bg-white text-gray-700 hover:bg-gray-50 transition">
                        <span x-text="isFullscreen ? '❌ Изход от цял екран' : '🔳 Голям екран'"></span>
                    </button>

                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Избери играч:</label>
                    <select x-model="selectedPlayerId"
                        class="border border-gray-300 rounded-lg p-2 text-sm text-gray-700 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition w-full max-w-sm"
                        :disabled="tool !== null">
                        <option :value="null" x-show="true">-- Избери играч --</option>
                        <template x-for="player in players" :key="player.id">
                            <option :value="player.id"
                                x-text="player.name + (player.position ? ' (' + player.position + ')' : '') + (player.number ? ' #' + player.number : '')">
                            </option>
                        </template>
                    </select>
                </div>
            </div>

            <div class="relative mx-auto" style="width: 1104px; height: 596px;">
                <img src="/images/cska-logo.png" alt="CSKA"
                    class="absolute top-2 right-2 w-16 h-16 opacity-90 z-10">
                <div id="tactic-stage" class="border-2 border-gray-300 rounded shadow-lg bg-white relative z-0"
                    style="width: 1104px; height: 596px;">
                </div>
            </div>

        </div>
    </div>
</div>
