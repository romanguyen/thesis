const IMPORTED_HARDWARE_DETAILS = {
  "carex.ibot.cas.cz": {
    cpu: "1x AMD EPYC 7261 8-Core Processor",
    ram: "512 GiB",
    storage: "2x 960GB NVMe",
    network: "Ethernet 1Gbit/s, Omni-Path",
    comment: "Cluster of computing machines.",
    owner: "Institute of Botany CAS",
    imageSrc: "https://www.metacentrum.cz/machines/carex-th.jpg"
  },
  "draba.ibot.cas.cz": {
    cpu: "4x Intel(R) Xeon(R) Gold 6230 20-Core Processor",
    ram: "1536 GiB",
    storage: "2x 960GB NVMe",
    network: "Ethernet 1Gbit/s, Omni-Path",
    comment: "A machine inteded for jobs requiring large amounts of memory.",
    owner: "Institute of Botany CAS",
    imageSrc: "https://www.metacentrum.cz/machines/draba-th.jpg"
  },
  "vinca.ibot.cas.cz": {
    cpu: "1x AMD EPYC 7302P 16-Core Processor",
    ram: "512 GiB",
    storage: "3x 1.9TB NVMe",
    network: "Ethernet 1Gbit/s, Omni-Path",
    comment: "Cluster of computing machines.",
    owner: "Institute of Botany CAS",
    imageSrc: "https://www.metacentrum.cz/machines/vinca.ibot.cas.cz-th.jpg"
  },
  "bee.cerit-sc.cz": {
    cpu: "2x AMD EPYC 9454 48-Core Processor",
    ram: "1536 GiB",
    gpu: "2x NVIDIA H100 96GB",
    storage: "8x 7TB",
    network: "Ethernet 100Gbit/s, InfiniBand 200Gbit/s",
    comment: "SPECrate 2017_fp_base performance of each node = 1060",
    owner: "CERIT-SC",
    imageSrc: "https://www.metacentrum.cz/static/images/bee.cerit-sc.cz.jpg"
  },
  "grogu.cerit-sc.cz (2026)": {
    cpu: "2x AMD EPYC 9454 48-Core Processor",
    ram: "1536 GiB",
    storage: "6x 7TB NVMe",
    network: "Ethernet 100Gbit/s",
    comment: "SPECrate 2017_fp_base performance of each node = 1100",
    owner: "CERIT-SC",
    imageSrc: "https://www.metacentrum.cz/machines/grogu.cerit-sc.cz-th.jpg"
  },
  "capy.cerit-sc.cz": {
    cpu: "2x Intel(R) Xeon(R) Platinum 8480CL 56-Core Processor",
    ram: "2048 GiB",
    gpu: "8x NVIDIA H100 80GB",
    storage: "30 TB NVMe",
    network: "Ethernet 100Gbit/s",
    comment: "For access, please contact meta@cesnet.cz",
    owner: "CERIT-SC",
    imageSrc: "https://www.metacentrum.cz/machines/capy.cerit-sc.cz-th.jpg"
  },
  "glados.cerit-sc.cz": {
    cpu: "2x Intel Xeon Gold 6138 20-Core Processor",
    ram: "384 GB",
    storage: "2x2TB SSD NVMe",
    network: "Ethernet 10Gb",
    comment: "SPECrate 2006_fp_base performance of each node = 1370",
    owner: "CERIT-SC",
    imageSrc: "https://www.metacentrum.cz/machines/glados_ostack.jpg"
  },
  "uruk.cerit-sc.cz": {
    cpu: "8x Intel Xeon Gold 6140 18-Core Processor",
    ram: "2.91 TiB",
    storage: "2x1.82 TiB SSD NVME, 2x 447.13 GiB 7.2, 4x 3.64 TiB 7.2",
    network: "4x Ethernet 10 Gbit/s",
    comment: "Performence of node: SPECfp2017: 700 (4.86 per core)",
    owner: "CERIT-SC",
    imageSrc: "https://www.metacentrum.cz/machines/uruk-th.jpg"
  },
  "ursa.cerit-sc.cz": {
    cpu: "28x Intel Xeon Gold 6254 18-Core Processor",
    ram: "9.90 TiB",
    storage: "11x2.91 TiB SSD NVME, 2x 447.13 GiB 7.2",
    network: "1x InfiniBand 100 Gbit/s, 4x Ethernet 10 Gbit/s",
    comment: "Performence of node: SPECfp2017: 2160 (4.29 per core)",
    owner: "CERIT-SC",
    imageSrc: "https://www.metacentrum.cz/machines/ursa-th.jpg"
  },
  "zenon.cerit-sc.cz": {
    cpu: "2x AMD EPYC 7351 16-Core Processor",
    ram: "512 GB",
    storage: "2 TB SSD",
    network: "1x InfiniBand 56 Gbit/s, Ethernet 1Gbit/s",
    comment: "SPECfp2006 performance of each node is 1320",
    owner: "CERIT-SC"
  },
  "zia.cerit-sc.cz": {
    cpu: "2x AMD EPYC 7662 64-Core Processor",
    ram: "1007.68 GiB",
    gpu: "4x NVIDIA A100 40GB",
    storage: "3x1.46 TiB SSD NVME",
    network: "2x Ethernet 10 Gbit/s",
    comment: "Performence of each node: SPECfp2017: 527 (4.1 per core)",
    owner: "CERIT-SC",
    imageSrc: "https://www.metacentrum.cz/static/images/zia.cerit-sc.cz.jpg"
  },
  "ics-gladosag-ostack.priv.cloud.muni.cz": {
    cpu: "2x Intel Xeon Gold 6138 (2x 20 Core) 2.0 GHz",
    ram: "384 GB",
    storage: "2x2TB SSD NvmE",
    network: "1x10Gb Ethernet",
    comment: "Performence of each node: SPECfp2006: 1370 (34.25 per core), the machines marked \"ag\" have Nvidia 1080Ti GPU",
    owner: "CERIT-SC/MU",
    imageSrc: "https://www.metacentrum.cz/machines/glados_ostack-th.jpg"
  },
  "eli-hda1-ostack.priv.cloud.muni.cz": {
    cpu: "2x Intel Xeon Processor (2x 16) 2.2 GHz",
    ram: "768 GB",
    storage: "1x1.8 TiB SSD SATA",
    network: "1x Ethernet 10 Gbit/s",
    comment: "Performance of each node: SPECfp2017: 167 (5.2 per core)",
    owner: "CERIT-SC/MU"
  },
  "cerit-hdg-ostack.priv.cloud.muni.cz": {
    cpu: "2x AMD EPYC Processor (with IBPB) (2x 16 Core)",
    ram: "472.40 GiB",
    storage: "Virtual disks",
    network: "",
    comment: "Performance of each node: SPECfp2017: 332 (10.4 per core)",
    owner: "CERIT-SC/MU"
  },
  "adan.grid.cesnet.cz (2025)": {
    cpu: "2x AMD EPYC 9554 64-Core Processor",
    ram: "768 GiB",
    storage: "2x 3.84 TB NVMe",
    network: "Ethernet 25Gbit/s",
    comment: "SPECfp2017 performance of each node: 1360 (10.6 per core)",
    owner: "CESNET",
    imageSrc: "https://www.metacentrum.cz/static/images/adan.grid.cesnet.cz.jpg"
  },
  "aman.ics.muni.cz": {
    cpu: "4x Intel Xeon E7-4830 v4 14-Core Processor",
    ram: "512 GB",
    storage: "2x 4TB 7k2 SATA III, 480 GB Intel SSD S3610",
    network: "Ethernet 1Gbit/s",
    comment: "SPECfp2006 performance of each node: 1490 (26.6 per core)",
    owner: "CESNET",
    imageSrc: "https://www.metacentrum.cz/machines/aman-th.jpg"
  },
  "tarkil.grid.cesnet.cz": {
    cpu: "2x Intel Xeon E5-2650v4 12-Core Processor",
    ram: "128GB",
    storage: "2x 4TB SATA 7200 ot/min",
    network: "Ethernet 1Gb/s, Infiniband Mellanox FDR (56Gb/s)",
    comment: "SPECfp2006 performance of each node: 790 (32.9 per core)",
    owner: "CESNET",
    imageSrc: "https://www.metacentrum.cz/machines/tarkil2016-th.jpg"
  },
  "oven.grid.cesnet.cz": {
    cpu: "Virtual CPUs",
    ram: "8 GB",
    storage: "30GB local scratch",
    network: "Ethernet 10Gbit/s",
    comment: "Virtual machine",
    owner: "CESNET"
  },
  "deimos.meta.zcu.cz (2026)": {
    cpu: "2x AMD EPYC 9555 64-Core Processor",
    ram: "768 GiB",
    storage: "2x 1.92 TB NVMe",
    network: "Ethernet 25Gbit/s, Infiniband HDR200",
    comment: "SPECrate 2017_fp_base performance of each node = 1700",
    owner: "CESNET",
    imageSrc: "https://www.metacentrum.cz/machines/deimos.meta.zcu.cz.jpg"
  },
  "fobos.meta.zcu.cz": {
    cpu: "2x AMD EPYC 9454 2.75GHz 48-Core Processor",
    ram: "768 GiB",
    gpu: "4x NVIDIA L40S 48GB",
    storage: "4x 3.84 NVMe",
    network: "Ethernet 100Gbit/s, InfiniBand 200Gbit/s",
    comment: "SPECrate 2017_fp_base performance of each node = 1160",
    owner: "CESNET",
    imageSrc: "https://www.metacentrum.cz/static/images/fobos.meta.zcu.cz.jpg"
  },
  "haldan.metacentrum.cz (2026)": {
    cpu: "2x AMD EPYC 9555 64-Core Processor",
    ram: "768 GiB",
    storage: "2x 1.92 TB NVMe",
    network: "Ethernet 10Gbit/s",
    comment: "SPECrate 2017_fp_base performance of each node = 1700",
    owner: "CESNET",
    imageSrc: "https://www.metacentrum.cz/machines/haldan.metacentrum.cz-th.jpg"
  },
  "halmir.metacentrum.cz": {
    cpu: "2x AMD EPYC 7543 32-Core Processor",
    ram: "1024 GiB",
    storage: "2x 7.68 TB NVMe",
    network: "Ethernet 10Gbit/s",
    comment: "SPECfp2017 performance of each node: 513 (8 per core)",
    owner: "CESNET",
    imageSrc: "https://www.metacentrum.cz/static/images/halmir.metacentrum.cz.jpg"
  },
  "galdor.metacentrum.cz": {
    cpu: "2x AMD EPYC 7543 32-Core Processor",
    ram: "512 GiB",
    gpu: "4x NVIDIA A40 48GB",
    storage: "2x 7.68 TB NVMe",
    network: "Ethernet 10Gbit/s",
    comment: "4x nVidia A40SPECfp2017 performance of each node: 512 (8 per core)",
    owner: "CESNET",
    imageSrc: "https://www.metacentrum.cz/static/images/galdor.metacentrum.cz.jpg"
  },
  "tyra.metacentrum.cz": {
    cpu: "2x AMD EPYC 7543 32-Core Processor",
    ram: "512 GiB",
    storage: "2x 3.84TB NVMe",
    network: "Ethernet 10Gbit/s",
    comment: "SPECrate 2017_fp_base performance of each node = 516",
    owner: "CESNET",
    imageSrc: "https://www.metacentrum.cz/machines/tyra.metacentrum.cz-th.jpg"
  },
  "turin.grid.cesnet.cz": {
    cpu: "2x AMD EPYC 7543 32-Core Processor",
    ram: "512 GiB",
    storage: "2x 3.84TB NVMe",
    network: "Ethernet 10Gbit/s",
    comment: "SPECrate 2017_fp_base performance of each node = 516",
    owner: "CESNET",
    imageSrc: "https://www.metacentrum.cz/machines/tyra.metacentrum.cz-th.jpg"
  },
  "grimbold.ics.muni.cz": {
    cpu: "2x Intel Xeon Gold 6130 16-Core Processor",
    ram: "196GB",
    gpu: "2x NVIDIA Tesla P100 12GB",
    storage: "2x 4TB 7k2 SATA III",
    network: "ethernet 1GB/s",
    owner: "CESNET",
    imageSrc: "https://www.metacentrum.cz/static/images/grimbold.ics.muni.cz.jpg"
  },
  "hildor.metacentrum.cz (2026)": {
    cpu: "2x AMD EPYC 9555 64-Core Processor",
    ram: "768 GiB",
    storage: "2x 1.92 TB NVMe",
    network: "Ethernet 25Gbit/s",
    comment: "Vykon kazdeho uzlu je dle SPECrate 2017_fp_base = 1700",
    owner: "CESNET",
    imageSrc: "https://www.metacentrum.cz/machines/hildor-th.jpg"
  },
  "elbi1.hw.elixir-czech.cz": {
    cpu: "2x AMD EPYC 9474F 48-Core Processor",
    ram: "1536 GiB",
    gpu: "2x NVIDIA A100 40GB",
    storage: "8x 7TB NVMe",
    network: "Ethernet 10Gbit/s",
    comment: "SPECrate 2017_fp_base performance of each node = 1240",
    owner: "ELIXIR",
    imageSrc: "https://www.metacentrum.cz/machines/elbi1.hw.elixir-czech.cz-th.jpg"
  },
  "elmu1.hw.elixir-czech.cz": {
    cpu: "2x AMD EPYC 9474F 48-Core Processor",
    ram: "1536 GiB",
    storage: "2x 7TB NVMe",
    network: "Ethernet 10Gbit/s",
    comment: "SPECrate 2017_fp_base performance of each node = 1260",
    owner: "ELIXIR",
    imageSrc: "https://www.metacentrum.cz/machines/elmu1.hw.elixir-czech.cz-th.jpg"
  },
  "eluo1.hw.elixir-czech.cz": {
    cpu: "2x AMD EPYC 9474F 48-Core Processor",
    ram: "1536 GiB",
    storage: "8x 7TB NVMe",
    network: "Ethernet 10Gbit/s",
    comment: "SPECrate 2017_fp_base performance of each node = 1170",
    owner: "ELIXIR",
    imageSrc: "https://www.metacentrum.cz/machines/eluo1.hw.elixir-czech.cz-th.jpg"
  },
  "elum1.hw.elixir-czech.cz": {
    cpu: "2x AMD EPYC 9474F 48-Core Processor",
    ram: "1536 GiB",
    storage: "8x 7TB NVMe",
    network: "Ethernet 10Gbit/s",
    comment: "SPECrate 2017_fp_base performance of each node = 1170",
    owner: "ELIXIR"
  },
  "elwe.hw.elixir-czech.cz": {
    cpu: "2x AMD EPYC 7532 32-Core Processor",
    ram: "2TB",
    storage: "2xNVMe 7.68TB + 2x240GB SSD",
    network: "10Gb/s ethernet",
    comment: "SPECrate 2017_fp_base performance of each node = 452",
    owner: "ELIXIR"
  },
  "elmo5.hw.elixir-czech.cz": {
    cpu: "2x Intel Xeon Gold 6130 16-Core Processor",
    ram: "192 GB RAM",
    storage: "2x4TB disc",
    network: "1x Infiniband 56 Gbit/s, 1x Ethernet 1Gb/s",
    comment: "SPECfp2006 performance of each node: 1220 (38,13 per core)",
    owner: "ELIXIR"
  },
  "elmo4.hw.elixir-czech.cz": {
    cpu: "2x Intel Xeon Gold 5118 12-Core Processor",
    ram: "384 GB RAM",
    storage: "2x4TB disc",
    network: "1x Infiniband 56 Gbit/s, 1x Ethernet 1Gb/s",
    comment: "SPECfp2006 performance of each node: 990 (41,25 per core)",
    owner: "ELIXIR"
  },
  "eltu.hw.elixir-czech.cz": {
    cpu: "4x Intel(R) Xeon(R) Platinum 8260 24-Core Processor",
    ram: "3 TB",
    storage: "2x240 GB disk + 2x7 TB NVMe disk",
    network: "Ethernet 10 Gbit/s",
    comment: "SPECrate 2017_fp_base performance of each node = 509",
    owner: "ELIXIR"
  },
  "elmo3.hw.elixir-czech.cz": {
    cpu: "4x Intel Xeon Gold 5120 14-Core Processor",
    ram: "768 GB RAM",
    storage: "2x4TB disc + 2x480 SSD disc",
    network: "x Infiniband 56 Gbit/s, 1x Ethernet 1Gb/s, 1x Ethernet 10 Gbit/s",
    comment: "SPECfp2006 performance of each node: 2300 (41,07 per core)",
    owner: "ELIXIR"
  },
  "elmo2.hw.elixir-czech.cz": {
    cpu: "2x Intel Xeon Gold 5118 12-Core Processor",
    ram: "384 GB RAM",
    storage: "2x4TB disc",
    network: "1x Infiniband 56 Gbit/s, 1x Ethernet 1Gb/s",
    comment: "SPECfp2006 performance of each node: 990 (41,25 per core)",
    owner: "ELIXIR"
  },
  "elmo1.hw.elixir-czech.cz": {
    cpu: "4x Intel Xeon Gold 5120 14-Core Processor",
    ram: "768 GB RAM",
    storage: "2x4TB disc + 2x480 SSD disc",
    network: "1x Infiniband 56 Gbit/s, 1x Ethernet 1Gb/s, 1x Ethernet 10 Gbit/s",
    comment: "SPECfp2006 performance of each node: 2300 (41,07 per core)",
    owner: "ELIXIR"
  },
  "farin.grid.cesnet.cz": {
    cpu: "2x AMD EPYC 9554 64-Core Processor",
    ram: "2304 GiB",
    storage: "2x 7TB NVMe",
    network: "Ethernet 25Gbit/s",
    comment: "SPECrate 2017_fp_base performance of each node = 1300",
    owner: "Faculty of Civil Engineering CTU",
    imageSrc: "https://www.metacentrum.cz/static/images/farin.grid.cesnet.cz.jpg"
  },
  "luna2021.fzu.cz": {
    cpu: "2x AMD EPYC 7513 32-Core Processor",
    ram: "1024 GiB",
    storage: "2x 1.8TB SSD",
    network: "Ethernet 10Gbit/s",
    comment: "Supermicro",
    owner: "Institute of Physics CAS",
    imageSrc: "https://www.metacentrum.cz/machines/luna2021.jpg"
  },
  "luna2022.fzu.cz": {
    cpu: "2x AMD EPYC 7513 32-Core Processor",
    ram: "512 GiB",
    gpu: "1x NVIDIA A40 48GB",
    storage: "960 GB",
    network: "Ethernet 10Gbit/s",
    comment: "ASUSTeK",
    owner: "Institute of Physics CAS",
    imageSrc: "https://www.metacentrum.cz/machines/luna2022.fzu.cz-th.jpg"
  },
  "luna2023.fzu.cz": {
    cpu: "2x AMD EPYC 9474F 48-Core Processor",
    ram: "1536 GiB",
    storage: "1x 3.84 NVMe",
    network: "Ethernet 10Gbit/s",
    comment: "SPECrate 2017_fp_base performance of each node = 1110",
    owner: "Institute of Physics CAS",
    imageSrc: "https://www.metacentrum.cz/static/images/luna2023.fzu.cz.jpg"
  },
  "magma.fzu.cz": {
    cpu: "2x AMD EPYC 9454 48-Core Processor",
    ram: "1536 GiB",
    storage: "1x 3.84 NVMe",
    network: "Ethernet 10Gbit/s",
    comment: "SPECrate 2017_fp_base performance of each node = 1160",
    owner: "Institute of Physics CAS",
    imageSrc: "https://www.metacentrum.cz/static/images/magma.fzu.cz.jpg"
  },
  "cha.natur.cuni.cz": {
    cpu: "2x Intel(R) Xeon(R) Silver 4216 16-Core Processor",
    ram: "192 GiB",
    gpu: "8x NVIDIA GeForce RTX 2080 Ti 11GB",
    storage: "2x 960 SSD",
    network: "Ethernet 1Gbit/s",
    comment: "GPU computing node",
    owner: "Faculty of Science Charles University",
    imageSrc: "https://www.metacentrum.cz/machines/cha-th.jpg"
  },
  "mor.natur.cuni.cz": {
    cpu: "2x Intel(R) Xeon(R) Silver 4210 10-Core Processor",
    ram: "256 GiB",
    storage: "2x 4TB HDD",
    network: "Ethernet 1Gbit/s",
    comment: "Cluster of computing machines.",
    owner: "Faculty of Science Charles University",
    imageSrc: "https://www.metacentrum.cz/machines/mor-th.jpg"
  },
  "pcr.natur.cuni.cz": {
    cpu: "2x AMD EPYC 7452 32-Core Processor",
    ram: "256 GiB",
    storage: "2x 4TB HDD",
    network: "Ethernet 1Gbit/s",
    comment: "Cluster of computing machines.",
    owner: "Faculty of Science Charles University",
    imageSrc: "https://www.metacentrum.cz/machines/pcr-th.jpg"
  },
  "fau.natur.cuni.cz": {
    cpu: "2x AMD EPYC 7452 32-Core Processor",
    ram: "256 GiB",
    gpu: "8x Quadro RTX 5000 16GB",
    storage: "2x 960 SSD",
    network: "Ethernet 1Gbit/s",
    comment: "8x NVIDIA Quadro RTX 5000",
    owner: "Faculty of Science Charles University",
    imageSrc: "https://www.metacentrum.cz/machines/fau-th.jpg"
  },
  "fer.natur.cuni.cz": {
    cpu: "2x AMD EPYC 7532 32-Core Processor",
    ram: "256 GiB",
    gpu: "8x NVIDIA RTX A4000 16GB",
    storage: "2x 960GB",
    network: "Ethernet 1Gbit/s",
    comment: "8x NVIDIA RTX A4000",
    owner: "Faculty of Science Charles University",
    imageSrc: "https://www.metacentrum.cz/machines/fer.natur.cuni.cz.jpg"
  },
  "charon.nti.tul.cz": {
    cpu: "2x Intel Xeon Silver 4114 10-Core Processor",
    ram: "12x 8 GB DDR4 2400 ECC Reg dual rank",
    storage: "1x SSD 480 GB DC S3610 Series",
    network: "1 GB ethernet a Omni-Path (InfiniBand - Intel)",
    owner: "Technical University of Liberec",
    imageSrc: "https://www.metacentrum.cz/machines/charon-th.jpg"
  },
  "alfrid.meta.zcu.cz": {
    cpu: "2x AMD EPYC 9554 64-Core Processor",
    ram: "1536 GiB",
    gpu: "2x NVIDIA L40 48GB",
    storage: "2x 7TB NVMe",
    network: "Ethernet 25Gbit/s",
    comment: "2x nVidia L40SPECrate 2017_fp_base performance of each node = 1230",
    owner: "UWB",
    imageSrc: "https://www.metacentrum.cz/machines/alfrid.meta.zcu.cz-th.jpg"
  },
  "alfrid-II.meta.zcu.cz": {
    cpu: "2x AMD EPYC 9554 64-Core Processor",
    ram: "768 GiB",
    gpu: "4x NVIDIA L40S 48GB",
    storage: "2x 7TB NVMe",
    network: "Ethernet 25Gbit/s",
    comment: "4x nVidia L40SPECrate 2017_fp_base performance of each node = 1230",
    owner: "UWB",
    imageSrc: "https://www.metacentrum.cz/static/images/alfrid-II.meta.zcu.cz.jpg"
  },
  "alfrid-smp.meta.zcu.cz": {
    cpu: "2x AMD EPYC 9554 64-Core Processor",
    ram: "4608 GiB",
    storage: "2x 7TB NVMe",
    network: "Ethernet 10Gbit/s",
    comment: "SPECrate 2017_fp_base performance of each node = 1200",
    owner: "UWB",
    imageSrc: "https://www.metacentrum.cz/static/images/alfrid-SMP.meta.zcu.cz.png"
  },
  "konos.fav.zcu.cz": {
    cpu: "2x Intel(R) Xeon(R) CPU E5-2630 v4 10-Cores Processor",
    ram: "128 GB",
    storage: "2x 4TB SATA",
    network: "1 Gbit/s Ethernet",
    comment: "all nodes have 4x GPU NVIDIA GeForce GTX 1080 TiSPECfp2006 performance of each node: 850 (42.5 per core)",
    owner: "UWB",
    imageSrc: "https://www.metacentrum.cz/machines/konos1-10-th.jpg"
  },
  "samson.ueb.cas.cz": {
    cpu: "4x Intel Xeon Platinum 8280 28-Core Processor",
    ram: "1007.44 GiB",
    storage: "6x1.46 TiB SSD NVME",
    network: "2x Ethernet 5 Gbit/s",
    comment: "Performence of each node: SPECfp2017: 557 (4.97 per core)",
    owner: "Institute of Experimental Botany CAS"
  }
};

const LABELS = {
  en: {
    cpu: "CPU",
    ram: "RAM",
    gpu: "GPU",
    storage: "disk",
    network: "net",
    comment: "comment",
    owner: "owner",
    resource: "resource",
    resourceLinkText: "Open resource page",
    detail: "Detail",
    nodes: "nodes",
    close: "Close",
    empty: "No additional details available.",
    detailAriaPrefix: "Detail for ",
  },
  cs: {
    cpu: "CPU",
    ram: "RAM",
    gpu: "GPU",
    storage: "disk",
    network: "sit",
    comment: "poznamka",
    owner: "vlastnik",
    resource: "odkaz",
    resourceLinkText: "Otevrit resource page",
    detail: "Detail",
    nodes: "uzly",
    close: "Zavrit",
    empty: "Dalsi informace nejsou k dispozici.",
    detailAriaPrefix: "Detail pro ",
  },
};

(() => {
  const site = document.getElementById("dokuwiki__site");
  if (!site) return;

  const isHardwarePage = Array.from(site.classList).some((className) =>
    /^reskin-pageid-(cs|en)-resources-hardware(?:-start)?$/.test(className)
  );
  if (!isHardwarePage) return;

  const locale = detectLocale(site);
  const labels = LABELS[locale] || LABELS.en;
  const catalogIndex = buildCatalogIndex(IMPORTED_HARDWARE_DETAILS);

  const page = site.querySelector(".reskin-page");
  if (!page) return;

  const inventoryTable = findInventoryTable(page);
  if (!inventoryTable) return;

  const profileMap = buildProfileMapFromTable(inventoryTable, catalogIndex, labels);
  if (profileMap.size === 0) return;

  const drawer = ensureDrawer(site, labels);
  const canOpenDrawer = !!(
    drawer &&
    window.bootstrap &&
    window.bootstrap.Offcanvas
  );

  let drawerApi = null;
  let drawerTitle = null;
  let drawerImage = null;
  let drawerTableBody = null;

  if (canOpenDrawer) {
    drawerApi = window.bootstrap.Offcanvas.getOrCreateInstance(drawer);
    drawerTitle = drawer.querySelector("[data-hw-drawer-title]");
    drawerImage = drawer.querySelector("[data-hw-drawer-image]");
    drawerTableBody = drawer.querySelector("[data-hw-drawer-specs]");

    if (drawerImage && !drawerImage.dataset.errorBound) {
      drawerImage.addEventListener("error", () => {
        drawerImage.setAttribute("hidden", "hidden");
        drawerImage.removeAttribute("src");
        drawerImage.alt = "";
      });
      drawerImage.dataset.errorBound = "1";
    }
  }

  appendDetailColumn(inventoryTable, profileMap, labels, (clusterKey) => {
    if (!canOpenDrawer || !drawerApi) return;

    const profile = profileMap.get(clusterKey);
    if (!profile) return;

    if (drawerTitle) drawerTitle.textContent = profile.title;

    if (drawerImage) {
      if (profile.imageSrc) {
        drawerImage.src = profile.imageSrc;
        drawerImage.alt = profile.imageAlt || profile.title;
        drawerImage.removeAttribute("hidden");
      } else {
        drawerImage.setAttribute("hidden", "hidden");
        drawerImage.removeAttribute("src");
        drawerImage.alt = "";
      }
    }

    if (drawerTableBody) {
      if (profile.specs.length === 0) {
        drawerTableBody.innerHTML = `<tr><th scope="row">Info</th><td>${escapeHtml(labels.empty)}</td></tr>`;
      } else {
        drawerTableBody.innerHTML = profile.specs
          .map(
            (spec) =>
              `<tr><th scope="row">${escapeHtml(spec.label)}</th><td>${spec.valueHtml}</td></tr>`
          )
          .join("");
      }
    }

    drawerApi.show();
  });
})();

function detectLocale(site) {
  return Array.from(site.classList).some((className) =>
    className.startsWith("reskin-pageid-cs-")
  )
    ? "cs"
    : "en";
}

function findInventoryTable(page) {
  const tables = Array.from(page.querySelectorAll("table"));
  for (const table of tables) {
    const headers = Array.from(table.querySelectorAll("tr:first-child th"))
      .map((th) => normalizeHeader(th.textContent))
      .filter(Boolean);

    const hasCluster = headers.includes("cluster");
    const hasCpu = headers.includes("cpu");
    const hasNodes =
      headers.includes("nodes") || headers.includes("uzly") || headers.includes("node");

    if (hasCluster && hasCpu && hasNodes) {
      return table;
    }
  }

  return null;
}

function normalizeHeader(text) {
  return (text || "").trim().toLowerCase().replace(/\s+/g, " ");
}

function buildProfileMapFromTable(table, catalogIndex, labels) {
  const map = new Map();

  const rows = Array.from(table.querySelectorAll("tr")).filter(
    (row, index) => index > 0 && row.querySelector("td")
  );

  rows.forEach((row) => {
    const clusterAnchor = findClusterAnchor(row);
    if (!clusterAnchor) return;

    const clusterName = clusterAnchor.textContent.trim();
    const clusterKey = normalizeClusterKey(clusterName);
    const details =
      catalogIndex[clusterKey] || catalogIndex[stripClusterYearSuffix(clusterKey)] || null;

    const cells = row.querySelectorAll("td");
    const fallback = {
      institution: cells[0] ? cells[0].textContent.trim() : "",
      cpu: cells[2] ? cells[2].textContent.trim() : "",
      nodes: cells[3] ? cells[3].textContent.trim() : "",
      liveUrl: clusterAnchor.href || "",
    };

    map.set(clusterKey, {
      title: clusterName,
      imageSrc: buildImageSource(details && details.imageSrc),
      imageAlt: clusterName,
      specs: buildSpecs(details, labels, fallback),
    });
  });

  return map;
}

function buildCatalogIndex(catalog) {
  const index = {};
  Object.entries(catalog).forEach(([rawKey, details]) => {
    const normalized = normalizeClusterKey(rawKey);
    index[normalized] = details;

    const noYear = stripClusterYearSuffix(normalized);
    if (noYear) {
      index[noYear] = details;
    }
  });
  return index;
}

function findClusterAnchor(row) {
  const anchors = Array.from(row.querySelectorAll("a"));
  return (
    anchors.find((anchor) => normalizeClusterKey(anchor.textContent).includes(".")) ||
    null
  );
}

function buildImageSource(mediaId) {
  if (!mediaId) return "";
  if (/^https?:\/\//i.test(mediaId)) {
    return mediaId.replace(/^http:\/\//i, "https://");
  }
  return `/lib/exe/fetch.php?w=860&h=280&media=${encodeURIComponent(mediaId)}`;
}

function buildSpecs(details, labels, fallback) {
  const cpu = details && details.cpu ? details.cpu : fallback.cpu;
  const ram = details && details.ram ? details.ram : "";
  const gpu = details && details.gpu ? details.gpu : "";
  const storage = details && details.storage ? details.storage : "";
  const network = details && details.network ? details.network : "";
  const comment = details && details.comment ? details.comment : "";
  const owner = details && details.owner ? details.owner : fallback.institution;
  const liveUrl = details && details.liveUrl ? details.liveUrl : fallback.liveUrl;
  const nodeCount = fallback.nodes;

  const specs = [];

  if (cpu) {
    specs.push({ label: labels.cpu, valueHtml: escapeHtml(cpu) });
  }

  if (nodeCount && nodeCount !== "-") {
    specs.push({ label: labels.nodes, valueHtml: escapeHtml(nodeCount) });
  }

  if (ram) {
    specs.push({ label: labels.ram, valueHtml: escapeHtml(ram) });
  }

  if (gpu) {
    specs.push({ label: labels.gpu, valueHtml: escapeHtml(gpu) });
  }

  if (storage) {
    specs.push({ label: labels.storage, valueHtml: escapeHtml(storage) });
  }

  if (network) {
    specs.push({ label: labels.network, valueHtml: escapeHtml(network) });
  }

  if (comment) {
    specs.push({ label: labels.comment, valueHtml: escapeHtml(comment) });
  }

  if (owner) {
    specs.push({ label: labels.owner, valueHtml: escapeHtml(owner) });
  }

  if (liveUrl) {
    specs.push({
      label: labels.resource,
      valueHtml: `<a href="${escapeAttr(liveUrl)}" class="urlextern" rel="ugc nofollow">${escapeHtml(labels.resourceLinkText)}</a>`,
    });
  }

  return specs;
}

function stripClusterYearSuffix(name) {
  return (name || "").replace(/\s*\(\d{4}\)$/, "").trim();
}

function normalizeClusterKey(name) {
  return (name || "")
    .trim()
    .toLowerCase()
    .replace(/\s+/g, " ");
}

function appendDetailColumn(table, profileMap, labels, onDetailClick) {
  const headRow = table.querySelector("tr:first-child");
  if (!headRow) return;

  if (!headRow.querySelector(".reskin-hw-detail-head")) {
    const headCell = document.createElement("th");
    headCell.className = "reskin-hw-detail-head";
    headCell.textContent = labels.detail;
    headRow.appendChild(headCell);
  }

  const rows = Array.from(table.querySelectorAll("tr")).filter(
    (row, index) => index > 0 && row.querySelector("td")
  );

  rows.forEach((row) => {
    if (row.querySelector(".reskin-hw-detail-cell")) return;

    const clusterAnchor = findClusterAnchor(row);
    const clusterName = clusterAnchor ? clusterAnchor.textContent.trim() : "";
    const clusterKey = normalizeClusterKey(clusterName);
    if (!clusterKey || !profileMap.has(clusterKey)) return;

    const cell = document.createElement("td");
    cell.className = "reskin-hw-detail-cell";

    const button = document.createElement("button");
    button.type = "button";
    button.className = "btn btn-sm btn-outline-secondary reskin-hw-detail-btn";
    button.textContent = labels.detail;
    button.setAttribute("aria-label", `${labels.detailAriaPrefix}${clusterName}`);
    button.addEventListener("click", () => onDetailClick(clusterKey));

    cell.appendChild(button);
    row.appendChild(cell);
  });
}

function ensureDrawer(site, labels) {
  let drawer = document.getElementById("reskinHardwareDrawer");
  if (drawer) return drawer;

  const drawerMarkup = `
    <div class="offcanvas offcanvas-end reskin-offcanvas reskin-hw-drawer" tabindex="-1" id="reskinHardwareDrawer" aria-labelledby="reskinHardwareDrawerLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="reskinHardwareDrawerLabel" data-hw-drawer-title></h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="${escapeAttr(labels.close)}"></button>
      </div>
      <div class="offcanvas-body">
        <img class="reskin-hw-drawer-image" data-hw-drawer-image alt="" hidden>
        <table class="table table-sm align-middle reskin-hw-drawer-table">
          <tbody data-hw-drawer-specs></tbody>
        </table>
      </div>
    </div>
  `;

  site.insertAdjacentHTML("beforeend", drawerMarkup);
  drawer = document.getElementById("reskinHardwareDrawer");
  return drawer;
}

function escapeHtml(value) {
  const text = String(value == null ? "" : value);
  return text
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#39;");
}

function escapeAttr(value) {
  return escapeHtml(value);
}
