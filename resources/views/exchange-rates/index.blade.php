@extends('layouts.app')

@section('title', 'Gestión de Tasa de Cambio')
@section('subtitle', 'Control de tasa USD a BsF')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-exchange-alt me-2"></i>
                        Gestión de Tasa de Cambio USD/BSF
                    </h5>
                </div>
                <div class="card-body" x-data="exchangeRateSystem()">
                    <!-- Estado Actual -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="card border-success">
                                <div class="card-body">
                                    <h6 class="card-title text-success">
                                        <i class="fas fa-chart-line me-2"></i>
                                        Tasa Actual
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h3 class="text-primary mb-0" x-text="'$1 USD = ' + currentRate.usd_to_bsf + ' BsF'"></h3>
                                        </div>
                                        <div class="col-md-6">
                                            <span class="badge" 
                                                  :class="currentRate.is_automatic ? 'bg-success' : 'bg-warning'"
                                                  x-text="currentRate.is_automatic ? 'Automática' : 'Manual'">
                                            </span>
                                            <br>
                                            <small class="text-muted" x-text="'Fuente: ' + (currentRate.source === 'bcv' ? 'BCV' : 'Manual')"></small>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            Última actualización: 
                                            <span x-text="formatDate(currentRate.last_updated_at)"></span>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-info">
                                <div class="card-body text-center">
                                    <h6 class="card-title text-info">
                                        <i class="fas fa-sync-alt me-2"></i>
                                        Actualizar desde BCV
                                    </h6>
                                    <button @click="updateFromBCV()" 
                                            :disabled="isUpdating"
                                            class="btn btn-info btn-lg w-100">
                                        <i class="fas fa-download me-2" x-show="!isUpdating"></i>
                                        <i class="fas fa-spinner fa-spin me-2" x-show="isUpdating"></i>
                                        <span x-text="isUpdating ? 'Actualizando...' : 'Actualizar desde BCV'"></span>
                                    </button>
                                    <small class="text-muted d-block mt-2">
                                        Obtiene la tasa oficial del Banco Central de Venezuela
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Configuración Manual -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-edit me-2"></i>
                                        Configuración Manual
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <form @submit.prevent="updateRate()">
                                        <div class="mb-3">
                                            <label class="form-label">Tasa USD a BsF</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$1 USD =</span>
                                                <input type="number" 
                                                       x-model="manualRate" 
                                                       step="0.01" 
                                                       min="0.01"
                                                       class="form-control"
                                                       placeholder="36.50">
                                                <span class="input-group-text">BsF</span>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       x-model="isAutomatic"
                                                       id="isAutomatic">
                                                <label class="form-check-label" for="isAutomatic">
                                                    Usar actualización automática desde BCV
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" 
                                                :disabled="isSaving"
                                                class="btn btn-primary w-100">
                                            <i class="fas fa-save me-2" x-show="!isSaving"></i>
                                            <i class="fas fa-spinner fa-spin me-2" x-show="isSaving"></i>
                                            <span x-text="isSaving ? 'Guardando...' : 'Guardar Configuración'"></span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-calculator me-2"></i>
                                        Calculadora de Conversión
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Convertir USD a BsF</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" 
                                                   x-model="usdAmount" 
                                                   step="0.01"
                                                   @input="calculateBsF()"
                                                   class="form-control"
                                                   placeholder="0.00">
                                        </div>
                                        <div class="mt-2">
                                            <strong>Resultado:</strong> 
                                            <span class="text-success" x-text="bsfResult + ' BsF'"></span>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Convertir BsF a USD</label>
                                        <div class="input-group">
                                            <span class="input-group-text">BsF</span>
                                            <input type="number" 
                                                   x-model="bsfAmount" 
                                                   step="0.01"
                                                   @input="calculateUSD()"
                                                   class="form-control"
                                                   placeholder="0.00">
                                        </div>
                                        <div class="mt-2">
                                            <strong>Resultado:</strong> 
                                            <span class="text-primary" x-text="'$' + usdResult"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function exchangeRateSystem() {
    return {
        currentRate: @json($exchangeRate),
        manualRate: @json($exchangeRate->usd_to_bsf),
        isAutomatic: @json($exchangeRate->is_automatic),
        isUpdating: false,
        isSaving: false,
        usdAmount: 0,
        bsfAmount: 0,
        bsfResult: 0,
        usdResult: 0,
        
        init() {
            this.calculateBsF();
            this.calculateUSD();
        },
        
        async updateFromBCV() {
            this.isUpdating = true;
            try {
                const response = await fetch('/exchange-rates/update-from-bcv', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.currentRate = data.rate;
                    this.manualRate = data.rate.usd_to_bsf;
                    this.isAutomatic = data.rate.is_automatic;
                    this.showAlert('success', data.message);
                    this.calculateBsF();
                    this.calculateUSD();
                } else {
                    this.showAlert('error', data.message);
                }
            } catch (error) {
                this.showAlert('error', 'Error al actualizar desde BCV');
            } finally {
                this.isUpdating = false;
            }
        },
        
        async updateRate() {
            this.isSaving = true;
            try {
                const response = await fetch('/exchange-rates/update', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        usd_to_bsf: this.manualRate,
                        is_automatic: this.isAutomatic
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.currentRate = data.rate;
                    this.showAlert('success', data.message);
                    this.calculateBsF();
                    this.calculateUSD();
                } else {
                    this.showAlert('error', data.message);
                }
            } catch (error) {
                this.showAlert('error', 'Error al guardar la configuración');
            } finally {
                this.isSaving = false;
            }
        },
        
        calculateBsF() {
            this.bsfResult = (this.usdAmount * this.currentRate.usd_to_bsf).toFixed(2);
        },
        
        calculateUSD() {
            this.usdResult = (this.bsfAmount / this.currentRate.usd_to_bsf).toFixed(2);
        },
        
        formatDate(dateString) {
            if (!dateString) return 'Nunca';
            const date = new Date(dateString);
            return date.toLocaleString('es-VE');
        },
        
        showAlert(type, message) {
            // Crear alerta temporal
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
            alertDiv.style.top = '20px';
            alertDiv.style.right = '20px';
            alertDiv.style.zIndex = '9999';
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(alertDiv);
            
            // Remover después de 5 segundos
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.parentNode.removeChild(alertDiv);
                }
            }, 5000);
        }
    }
}
</script>
@endsection
