@extends('layouts.main')

@section('content')
    <!-- CONTENT HEADER -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Servers</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('servers.index') }}">Servers</a>
                        <li class="breadcrumb-item"><a class="text-muted"
                                href="{{ route('servers.create') }}">Create</a>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <!-- END CONTENT HEADER -->

    <!-- MAIN CONTENT -->
    <section class="content">
        <div class="container-fluid">

            <!-- CUSTOM CONTENT -->
            <div class="row justify-content-center">
                <div class="card col-lg-8 col-md-12 mb-5">
                    <div class="card-header">
                        <h5 class="card-title"><i class="fa fa-server mr-2"></i>Create Server</h5>
                    </div>
                    <div class="card-body">

                        <div class="form-group" id="step_0">
                                <label for="name">Name</label>
                                <input id="name" name="name" type="text" required="required" placeholder="Enter the name of your server"
                                    class="form-control @error('name') is-invalid @enderror">
                        </div>

                        <div class="form-group" id="step_1" style="display: none">
                            <label for="location_id">Server location</label>
                            <div>

                                <select id="node_id" name="node_id" required="required"
                                    class="custom-select @error('node_id') is-invalid @enderror">
                                    <option selected disabled hidden value="">Please Select ...</option>
                                    @foreach ($locations as $location)
                                        <optgroup label="{{ $location->name }}">
                                            @foreach ($location->nodes as $node)
                                                @if (!$node->disabled)
                                                    <option value="{{ $node->id }}" selected>{{ $node->name }}</option>
                                                @endif
                                            @endforeach
                                        </optgroup>
                                    @endforeach

                                </select>
                            </div>

                            @error('node_id')
                                <div class="invalid-feedback">
                                    Please fill out this field.
                                </div>
                            @enderror
                        </div>
                        <div class="form-group" id="step_2" style="display: none">
                            <h4 class="text-center">Select Service</h4>
                            <div class="card" style="width: 18rem; display: inline-block; margin: 1rem;">
                                <img src="https://upload.craftions.net/file/8d0csjnA4l/hosting-server-minecraft.png" class="card-img-top" width="128">
                                <div class="card-body">
                                    <h5 class="card-title">Minecraft</h5>
                                    <p class="card-text">Host your own Minecraft Server in the Cloud.</p>
                                    <a href="javascript:selectService(0)" class="btn btn-primary">Select</a>
                                </div>
                            </div>
                            <!--
                            <div class="card" style="width: 18rem; display: inline-block; margin: 1rem;">
                                <img src="https://upload.craftions.net/file/du0VV60JEF/hosting-server-webspace.png" class="card-img-top" width="128">
                                <div class="card-body">
                                    <h5 class="card-title">Webhosting</h5>
                                    <p class="card-text">Host your own Website in the Cloud.</p>
                                    <a href="javascript:selectService(1)" class="btn btn-primary">Select</a>
                                </div>
                            </div>
                            -->
                        </div>
                        <div class="form-group" id="step_3" style="display: none">
                            <h4 class="text-center">Select Resources</h4>
                            <div id="sel_res_cards">
                            </div>
                        </div>
                        <div class="form-group" id="step_4" style="display: none">
                            <h4 class="text-center">Select Version</h4>
                            <div id="sel_ver_cards">
                            </div>
                        </div>
                        <div class="invalid-feedback" id="inv-feed" style="display: none">
                            Please fill out this field.
                        </div>
                        <div class="form-group text-right" id="con_btn">
                                <input type="button" class="btn btn-primary mt-3" value="Next"
                                    onclick="nextStep();">
                        </div>
                    </div>
                </div>
                <form id="h-f-sbm" style="display: none;" method="post" action="{{ route('servers.store') }}">
                    @csrf
                    <input id="form-name" name="name" type="text">
                    <input id="form-description" name="description" type="text">
                    <input id="form-node_id" name="node_id">
                    <input id="form-egg_id" name="egg_id">
                    <input id="form-product_id" name="product_id">
                </form>
            </div>
            <!-- END CUSTOM CONTENT -->


        </div>
        <script>

            let currentStep = 0;
            let currentService = 0;
            let currentResource = 0;
            let currentVersion = 0;

            const services = [
                "minecraft",
                "webspace"
            ]

            const versions = {
                minecraft: {
                    image: "https://mctzock.de/static/media/minecraft.a9bb9ba9.png",
                    vanilla: {
                        display: "Vanilla",
                        description: "Play Minecraft vanilla!",
                        image: "https://mctzock.de/static/media/minecraft.a9bb9ba9.png"
                    },
                    paper: {
                        display: "Paper",
                        description: "Play Minecraft with plugins!",
                        image: "https://avatars.githubusercontent.com/u/7608950?s=400&v=4"
                    },
                    forge: {
                        display: "Forge",
                        description: "Play Minecraft with mods!",
                        image: "https://files.minecraftforge.net/static/images/logo.svg"
                    }
                },
                webspace: {
                    image: "https://upload.craftions.net/file/du0VV60JEF/hosting-server-webspace.png",
                    blank: {
                        display: "Blank",
                        description: "Blank Webspace with PHP",
                        image: "https://upload.craftions.net/file/du0VV60JEF/hosting-server-webspace.png"
                    }
                }
            }

            const resources = {
                minecraft: {
                    small: {
                        display: "Small",
                        CPU: 50,
                        RAM: 1024,
                        Disk: 1024,
                        Databases: 0,
                        Backups: 1,
                        prize: 25
                    },
                    medium: {
                        display: "Medium",
                        CPU: 100,
                        RAM: 2048,
                        Disk: 4096,
                        Databases: 1,
                        Backups: 4,
                        prize: 55
                    },
                    large: {
                        display: "Large",
                        CPU: 150,
                        RAM: 4096,
                        Disk: 8192,
                        Databases: 4,
                        Backups: 8,
                        prize: 100
                    },
                    mega: {
                        display: "Mega",
                        CPU: 200,
                        RAM: 8192,
                        Disk: 16384,
                        Databases: 8,
                        Backups: 20,
                        prize: 187
                    }
                },
                webspace: {
                    small: {
                        display: "Small",
                        CPU: 10,
                        RAM: 256,
                        Disk: 512,
                        Databases: 0,
                        Backups: 0,
                        prize: 12
                    },
                    medium: {
                        display: "Medium",
                        CPU: 20,
                        RAM: 512,
                        Disk: 2048,
                        Databases: 0,
                        Backups: 1,
                        prize: 22
                    },
                    large: {
                        display: "Large",
                        CPU: 20,
                        RAM: 512,
                        Disk: 4096,
                        Databases: 2,
                        Backups: 4,
                        prize: 33
                    },
                    mega: {
                        display: "Mega",
                        CPU: 20,
                        RAM: 512,
                        Disk: 8192,
                        Databases: 2,
                        Backups: 8,
                        prize: 51
                    }
                }
            }

            const elements = [
                document.getElementById("name"),
                document.getElementById("node_id"),
                document.getElementById("egg_id"),
                document.getElementById("product_id"),
            ]

            const ids = {
                minecraft: {
                    eggs: {
                        vanilla: 5,
                        paper: 3,
                        forge: 2
                    },
                    products: {
                        small: "Llwiwl1wpO9002UiLk-W3",
                        medium: "tqKvbHRtGXGyeF3E3wpUh",
                        large: "FTpULvx3qu0HslkWh4q8i",
                        mega: "knaHw9wdh9psbgyRNIZ_d"
                    }
                },
                webspace: {
                    eggs: {
                        blank: 24
                    },
                    products: {
                        small: "NlN0n9jI-sO6HDEF4U1jA",
                        medium: "Nz-kcg_JnbGE4UWo-xBqk",
                        large: "f0pDmE7FpmJjeQwz56RbK",
                        mega: "KKFBcDD-xpDChMfkGp376"
                    }
                }
            }

            function nextStep(force = false) {
                document.getElementById("inv-feed").style.display = 'none'
                document.getElementById("con_btn").style.display = 'block';
                if(currentStep != 4){
                    if(currentStep != 2 && currentStep != 3){
                        if(!check(currentStep)){
                            document.getElementById("inv-feed").style.display = 'block'
                            return;
                        }
                    }
                    currentStep++;
                    document.getElementById("step_" + (currentStep - 1)).style.display = 'none';
                    document.getElementById("step_" + currentStep).style.display = 'block';
                }else {
                    submit();
                }
                if(currentStep === 2 || currentStep === 3 || currentStep === 4){
                    document.getElementById("con_btn").style.display = 'none';
                }
            }

            function selectService(id) {
                currentService = id;
                nextStep();

                let s = ""
                Object.keys(resources[services[currentService]]).forEach(x => {
                    s += `
                        <div class="card" style="width: 18rem; display: inline-block; margin: 1rem;">
                            <img src="${versions[services[currentService]].image}" class="card-img-top" width="128">
                            <div class="card-body">
                                <h5 class="card-title">${resources[services[currentService]][x].display}</h5>
                                <ul class="card-text">
                                    <li>CPU: ${resources[services[currentService]][x].CPU}%</li>
                                    <li>RAM: ${resources[services[currentService]][x].RAM}MB</li>
                                    <li>Disk: ${resources[services[currentService]][x].Disk}MB</li>
                                    <li>Databases: ${resources[services[currentService]][x].Databases}</li>
                                    <li>Backups: ${resources[services[currentService]][x].Backups}</li>
                                    <li><b>${resources[services[currentService]][x].prize} Credits/Month</b></li>
                                </ul>
                                <a href="javascript:selectResource('${x}')" class="btn btn-primary">Select</a>
                            </div>
                        </div>`
                })
                document.getElementById("sel_res_cards").innerHTML = s;
            }

            function selectResource(name) {
                currentResource = name;
                nextStep();
                let s = ""
                Object.keys(versions[services[currentService]]).forEach(x => {
                    if(x !== "image"){
                       s += `
                            <div class="card" style="width: 18rem; display: inline-block; margin: 1rem;">
                                <img src="${versions[services[currentService]][x].image}" class="card-img-top" width="128" height="256">
                                <div class="card-body">
                                    <h5 class="card-title">${versions[services[currentService]][x].display}</h5>
                                    <p class="card-text">
                                        ${versions[services[currentService]][x].description}
                                    </p>
                                    <a href="javascript:selectVersion('${x}')" class="btn btn-primary">Select</a>
                                </div>
                            </div>`
                    }
                })
                document.getElementById("sel_ver_cards").innerHTML = s;
            }

            function selectVersion(version) {
                console.log(version)
                console.log(ids[services[currentService]]);
                console.log(ids[services[currentService]].eggs);
                console.log(ids[services[currentService]].eggs[version]);
                document.getElementById("form-name").value = document.getElementById("name").value;
                document.getElementById("form-description").value = "";
                document.getElementById("form-node_id").value = document.getElementById("node_id").value;
                document.getElementById("form-egg_id").value = ids[services[currentService]].eggs[version];
                document.getElementById("form-product_id").value = ids[services[currentService]].products[currentResource];
                submit();
            }

            function check(step) {
                return elements[step].value != "" && elements[step].value != null;
            }

            function submit() {
                document.getElementById("h-f-sbm").submit();
            }
        </script>
    </section>
    <!-- END CONTENT -->

@endsection
